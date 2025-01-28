<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerificationTest extends KernelTestCase
{
    private EmailVerifier $emailVerifier;
    private EntityManagerInterface $entityManager;
    private VerifyEmailHelperInterface $verifyEmailHelper;

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

    public function testHandleEmailConfirmation(): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test@example.com');
        $user->setPassword('password123');
        $user->setIsVerified(false);

        // Simulate a signed URL in the request
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