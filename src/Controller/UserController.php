<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

/**
 * UserController
 * Gère les opérations liées aux utilisateurs.
 */
class UserController extends AbstractController
{
    /**
     * Liste tous les utilisateurs et permet d’ajouter un nouvel utilisateur.
     *
     * @Route("/dashboard/users", name="admin_user_list")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/dashboard/users', name: 'admin_user_list')]
    public function list(UserRepository $userRepository,  Request $request): Response
    {
        $users = $userRepository->findAll();

        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Compte activé',
                'required' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès.');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('administrator/users/dashboard_user.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier un utilisateur existant.
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/admin/user/{id}/edit', name: 'admin_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Compte activé',
                'required' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('administrator/users/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Supprime un utilisateur existant.
     *
     * @Route("/admin/user/{id}/delete", name="admin_user_delete", methods={"POST"})
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('admin_user_list');
    }
    
    /**
     * Affiche les leçons et certifications de l'utilisateur connecté.
     *
     * @Route("/my-lessons", name="user_lessons")
     * @param PurchaseRepository $purchaseRepository
     * @return Response
     */
    #[Route('/my-lessons', name: 'user_lessons')]
    public function myLessons(PurchaseRepository $purchaseRepository): Response
    {
        $user = $this->getUser();
        $purchases = $purchaseRepository->findBy([
            'user' => $user,
            'status' => 'completed',
        ]);

        $certifications = $user->getCertifications();
        return $this->render('lesson/my_lessons.html.twig', [
            'purchases' => $purchases,
            'certifications'=> $certifications
        ]);
    }
    
}