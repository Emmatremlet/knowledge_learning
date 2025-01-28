<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * EmailVerifier
 * Gère la vérification et l'envoi des e-mails de confirmation.
 */
class EmailVerifier
{
    /**
     * @param VerifyEmailHelperInterface $verifyEmailHelper Service d'aide à la vérification des e-mails.
     * @param MailerInterface $mailer Service de gestion de l'envoi des e-mails.
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine.
     */
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Envoie un e-mail de confirmation à l'utilisateur.
     *
     * @param string $verifyEmailRouteName Nom de la route utilisée pour la vérification.
     * @param User $user Utilisateur destinataire de l'e-mail.
     * @param TemplatedEmail $email Modèle d'e-mail à envoyer.
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);
        $this->mailer->send($email);
    }

    /**
     * Gère la confirmation de l'e-mail de l'utilisateur.
     *
     * @param Request $request Requête HTTP contenant les informations de confirmation.
     * @param User $user Utilisateur à vérifier.
     * @throws VerifyEmailExceptionInterface Si la vérification échoue.
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}