<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Entity\Theme;

/**
 * Class StripeControllerTest
 *
 * Cette classe teste les fonctionnalités liées au processus Stripe Checkout,
 * y compris la préparation des données de test, le passage à la caisse, et les cas où le panier est vide.
 *
 * @package App\Tests\Controller
 */
class StripeControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser Le client utilisé pour effectuer les requêtes HTTP.
     */
    private $client;

    /**
     * @var EntityManagerInterface Le gestionnaire d'entités utilisé pour manipuler la base de données.
     */
    private $entityManager;

    /**
     * @var User L'utilisateur utilisé pour les tests.
     */
    private $user;

    /**
     * @var Purchase L'achat utilisé pour les tests.
     */
    private $purchase;

    /**
     * @var Lesson La leçon associée à l'achat de test.
     */
    private $lesson;

    /**
     * @var Cursus Le cursus associé à la leçon et à l'achat de test.
     */
    private $cursus;

    /**
     * Nettoie les données de test après l'exécution des tests.
     *
     * Cette méthode supprime les entités créées dans la base de données pendant les tests.
     */
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

    /**
     * Prépare l'environnement de test.
     *
     * Cette méthode initialise le client, le gestionnaire d'entités, et les données nécessaires
     * pour les tests Stripe Checkout, incluant utilisateur, thème, cursus, leçon, et achat.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $this->prepareTestData();
    }

    /**
     * Prépare les données nécessaires pour les tests.
     *
     * Cette méthode crée les entités utilisateur, thème, cursus, leçon et achat dans la base de données.
     */
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

        $cursus = new Cursus();
        $cursus->setName('Cursus');
        $cursus->setTheme($theme);
        $cursus->setPrice(50);
        $theme->addCursus($cursus);
        $this->entityManager->persist($cursus);

        $lesson = new Lesson();
        $lesson->setName('Lesson');
        $lesson->setCursus($cursus);
        $lesson->setPrice(20);
        $lesson->setVideoUrl('url');
        $cursus->addLesson($lesson);
        $this->entityManager->persist($lesson);

        $purchase = new Purchase();
        $purchase->setUser($this->user);
        $purchase->setCursus($cursus);
        $purchase->setPurchaseDate(new \DateTimeImmutable());
        $purchase->setLesson($lesson);
        $purchase->setTotalPrice(50.0);

        $this->entityManager->persist($purchase);
        $this->entityManager->flush();

        $this->purchase = $purchase;
        $this->lesson = $lesson;
        $this->cursus = $cursus;
    }

    /**
     * Teste le processus Stripe Checkout.
     *
     * Cette méthode vérifie si l'utilisateur connecté peut effectuer un checkout Stripe
     * avec un achat existant, et si un ID de session est retourné.
     */
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

    /**
     * Teste le processus de checkout avec un panier vide.
     *
     * Cette méthode vérifie si une erreur appropriée est retournée lorsqu'un utilisateur
     * tente d'effectuer un checkout sans articles dans son panier.
     */
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