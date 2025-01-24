<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\LessonRepository;
use App\Repository\CursusRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        $user = $this->getUser();

        $purchases = $purchaseRepository->findBy(['user' => $user]);
        
        return $this->render('cart/index.html.twig', [
            'purchases' => $purchases,
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
}