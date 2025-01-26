<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Purchase;
use App\Entity\Lesson;
use App\Entity\Cursus;
use App\Repository\LessonRepository;
use App\Repository\CursusRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PurchaseController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        $user = $this->getUser();

        $purchases = $purchaseRepository->findBy([
            'user' => $user, 
            'status' => 'pending'
        ]);
        
        return $this->render('cart/index.html.twig', [
            'purchases' => $purchases,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? null
        ]);
    }

    #[Route('/cart/add/{type}/{id}', name: 'cart_add')]
    public function add(
        string $type,
        int $id,
        LessonRepository $lessonRepository,
        CursusRepository $cursusRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getIsVerified()) {
            $this->addFlash('danger', 'Votre compte n\'est pas activé. Veuillez vérifier votre email pour activer votre compte.');
            return $this->redirectToRoute('home');
        }

        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setPurchaseDate(new \DateTimeImmutable());

        if ($type === 'lesson') {
            $lesson = $lessonRepository->find($id);
            if (!$lesson) {
                throw $this->createNotFoundException('Lesson not found');
            }
            $purchase->setLesson($lesson);
            $purchase->setTotalPrice($lesson->getPrice());
        } elseif ($type === 'cursus') {
            $cursus = $cursusRepository->find($id);
            if (!$cursus) {
                throw $this->createNotFoundException('Cursus not found');
            }
            $purchase->setCursus($cursus);
            $purchase->setTotalPrice($cursus->getPrice());
        }
        $purchase->setStatus('pending');
        $entityManager->persist($purchase);
        $entityManager->flush();

        return $this->redirectToRoute('cart_index');
    }
    
    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id, PurchaseRepository $purchaseRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $purchase = $purchaseRepository->find($id);
        if (!$purchase || $purchase->getUser() !== $user) {
            throw $this->createNotFoundException('Purchase not found or access denied');
        }

        $entityManager->remove($purchase);
        $entityManager->flush();

        $this->addFlash('success', 'Item removed from cart successfully!');

        return $this->redirectToRoute('cart_index');
    }

     #[Route('/dashboard/purchases', name: 'admin_purchase_list')]
    public function list(PurchaseRepository $purchaseRepository, Request $request): Response
    {
        $purchases = $purchaseRepository->findBy([
            'status' => 'completed'
        ]);

        $purchase = new Purchase();
        $form = $this->createFormBuilder($purchase)
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'label' => 'Utilisateur',
            ])
            ->add('lesson', EntityType::class, [
                'class' => Lesson::class,
                'choice_label' => 'name',
                'label' => 'Leçon',
            ])
            ->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'name',
                'label' => 'Cursus',
            ])
            ->add('purchaseDate', DateTimeType::class, ['label' => 'Date d\'achat'])
            ->add('totalPrice', MoneyType::class, ['label' => 'Prix total'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($purchase);
            $entityManager->flush();

            $this->addFlash('success', 'Achat ajouté avec succès.');
            return $this->redirectToRoute('admin_purchase_list');
        }

        return $this->render('administrator/purchases/dashboard_purchase.html.twig', [
            'purchases' => $purchases,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/purchase/{id}/edit', name: 'admin_purchase_edit')]
    public function edit(Purchase $purchase, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($purchase)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'pending',
                    'Complété' => 'completed',
                ],
                'label' => 'Statut de l\'achat',
            ])
            ->add('purchaseDate', DateTimeType::class, ['label' => 'Date d\'achat'])
            ->add('totalPrice', MoneyType::class, ['label' => 'Prix total']);

        if ($purchase->getLesson()) {
            $form->add('lesson', EntityType::class, [
                'class' => Lesson::class,
                'choice_label' => 'name',
                'label' => 'Leçon associée',
            ]);
        } elseif ($purchase->getCursus()) {
            $form->add('cursus', EntityType::class, [
                'class' => Cursus::class,
                'choice_label' => 'name',
                'label' => 'Cursus associé',
            ]);
        }

        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Achat modifié avec succès.');
            return $this->redirectToRoute('admin_purchase_list');
        }

        return $this->render('administrator/purchases/edit_purchase.html.twig', [
            'form' => $form->createView(),
            'purchase' => $purchase,
        ]);
    }

    #[Route('/dashboard/purchase/{id}/delete', name: 'admin_purchase_delete', methods: ['POST'])]
    public function delete(Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($purchase);
        $entityManager->flush();

        $this->addFlash('success', 'Achat supprimé avec succès.');
        return $this->redirectToRoute('admin_purchase_list');
    }
}