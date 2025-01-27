<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Purchase;

class PurchaseTest extends WebTestCase
{
    private $user;
    private $client;
    private $entityManager;

    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userRepository = $this->entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['email' => 'testuser@example.com']);
        $this->user = $existingUser;

        if (!$existingUser) {
            $user = new User();
            $user->setName('user');
            $user->setEmail('testuser@example.com');
            $user->setPassword('password123');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->user = $user;
        }

        $cursus = new Cursus();
        $cursus->setName('Cursus Test');
        $cursus->setPrice(100);
        $this->entityManager->persist($cursus);

        $purchase = new Purchase();
        $purchase->setUser($user);
        $purchase->setCursus($cursus);
        $purchase->setPurchaseDate(new \DateTimeImmutable());
        $purchase->setTotalPrice(50.0);
        $this->entityManager->persist($purchase);

        $this->entityManager->flush();
    }

    public function testPurchaseProcess(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request('POST', '/login', [
            'email' => 'testuser2@example.com',
            'password' => 'password123',
        ]);
        $this->assertResponseStatusCodeSame(302);

        $this->client->request('GET', '/cart');
        $this->assertResponseIsSuccessful();
    }
}