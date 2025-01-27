<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Security\EmailVerifier;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;


class UserValidationTest extends KernelTestCase
{
    private EmailVerifier $emailVerifier;
    private VerifyEmailHelperInterface $verifyEmailHelper;


    protected function setUp(): void
    {
        self::bootKernel();
        $this->emailVerifier = static::getContainer()->get(EmailVerifier::class);
        $this->verifyEmailHelper = self::getContainer()->get('verify_email_helper');
    }

    public function testUserEmailValidation(): void
    {
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setIsVerified(false);

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'verify_email_route',
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        $request = new Request([], [], [], [], [], ['QUERY_STRING' => parse_url($signatureComponents->getSignedUrl(), PHP_URL_QUERY)]);

        $this->userService->handleEmailConfirmation($request, $user);

        $this->assertTrue($user->getIsVerified(), 'User should be verified after validation.');
    }
}