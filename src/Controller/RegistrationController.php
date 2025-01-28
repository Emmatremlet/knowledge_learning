<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * RegistrationController
 * Gère l'inscription et la vérification des utilisateurs.
 */
class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository; 
    }

    /**
     * Permet à un utilisateur de s'inscrire.
     *
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from('emmatremlet20@gmail.com')
                    ->to((string) $user->getEmail())
                    ->subject('Confirmation de votre inscription')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            
            $this->addFlash('success', 'Un e-mail de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Vérifie l'adresse e-mail de l'utilisateur après inscription.
     *
     * @Route("/verify/email", name="app_verify_email")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');

        return $this->redirectToRoute('home');
    }

    /**
     * Définit le rôle d'administrateur pour un utilisateur.
     *
     * @Route("/set-admin-role", name="set_admin_role")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function setAdminRoles(EntityManagerInterface $entityManager): Response
    {
        $user = $this->userRepository->findOneByEmail('admin@example.com');
        
        if ($user) {
            $roles = $user->getRoles();

            if (!in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_ADMIN';
                $user->setRoles($roles);

                $entityManager->flush();

                $this->addFlash('success', 'Rôle ADMIN ajouté à l\'utilisateur.');
            } else {
                $this->addFlash('info', 'L\'utilisateur a déjà le rôle ADMIN.');
            }
        } else {
            $this->addFlash('danger', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('home');
    }
}