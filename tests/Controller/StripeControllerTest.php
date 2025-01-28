<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Entity\Theme;


class StripeControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $user;
    private $purchase;
    private $lesson;
    private $cursus;

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->createQuery('DELETE FROM App\Entity\Purchase')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Lesson')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Cursus')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Theme')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $this->prepareTestData();
    }

    private function prepareTestData(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
        if (!$user) {
            $user = new User();
            $user->setName('Test User');
            $user->setEmail('testuser@example.com');
            $user->setPassword('password123');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        $this->user = $user;
        $this->client->loginUser($user);

        $theme = new Theme();
        $theme->setName('Theme');
        $this->entityManager->persist($theme);
        $this->entityManager->flush();  

        $cursus = new Cursus();
        $cursus->setName('Cursus');
        $cursus->setTheme($theme);
        $cursus->setPrice(50);
        $theme->addCursus($cursus);
        $this->entityManager->persist($cursus);
        $this->entityManager->persist($theme);
        $this->entityManager->flush();  

        $lesson = new Lesson();
        $lesson->setName('Lesson');
        $lesson->setCursus($cursus);
        $lesson->setPrice(20);
        $lesson->setVideoUrl('url');
        $cursus->addLesson($lesson);
        $this->entityManager->persist($lesson);
        $this->entityManager->persist($cursus);
        $this->entityManager->flush();  

        $purchase = new Purchase();
        $purchase->setUser($this->user);
        $purchase->setCursus($cursus);
        $purchase->setPurchaseDate(new \DateTimeImmutable());
        $purchase->setLesson($lesson);
        $purchase->setTotalPrice(50.0);

        $this->entityManager->persist($purchase);
        $this->entityManager->flush();   
        

        $this->user = $user;
        $this->purchase = $purchase;
        $this->lesson = $lesson;
        $this->cursus = $cursus;
    }

    public function testStripeCheckout(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request('POST', '/checkout', [
            'orderId' => $this->purchase->getId(),
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertNotEmpty($responseData['id'], 'The checkout ID should not be empty');
    }  
      
    public function testCheckoutWithEmptyCart(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request('POST', '/checkout');

        $this->assertResponseStatusCodeSame(400);

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('error', $responseContent);
        $this->assertEquals('Votre panier est vide.', $responseContent['error']);
    }   

}