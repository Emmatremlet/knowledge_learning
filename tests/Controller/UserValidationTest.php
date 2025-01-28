<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * EmailVerificationTest
 * Tests fonctionnels pour vérifier le processus de vérification d'email.
 */
class EmailVerificationTest extends KernelTestCase
{
    /** @var EmailVerifier Service de vérification des emails. */
    private EmailVerifier $emailVerifier;

    /** @var EntityManagerInterface Gestionnaire d'entités pour les tests. */
    private EntityManagerInterface $entityManager;

    /** @var VerifyEmailHelperInterface Service d'aide à la vérification des emails. */
    private VerifyEmailHelperInterface $verifyEmailHelper;

    /**
     * Prépare l'environnement de test avant chaque test.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->verifyEmailHelper = self::getContainer()->get(VerifyEmailHelperInterface::class);

        $this->emailVerifier = new EmailVerifier(
            $this->verifyEmailHelper,
            self::getContainer()->get('mailer.mailer'),
            $this->entityManager
        );
    }

    /**
     * Teste le processus de confirmation de l'email.
     *
     * Vérifie que l'utilisateur est marqué comme "vérifié" après confirmation.
     */
    public function testHandleEmailConfirmation(): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test@example.com');
        $user->setPassword('password123');
        $user->setIsVerified(false);

        $signedUrl = $this->verifyEmailHelper->generateSignature(
            'app_verify_email', 
            (string)$user->getId(),
            $user->getEmail()
        )->getSignedUrl();

        $request = Request::create($signedUrl);

        $this->emailVerifier->handleEmailConfirmation($request, $user);

        $this->assertTrue($user->getIsVerified());
    }
}