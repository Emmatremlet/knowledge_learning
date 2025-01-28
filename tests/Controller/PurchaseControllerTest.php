<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Purchase;

/**
 * Class PurchaseTest
 *
 * Cette classe teste le processus d'achat, y compris la création d'un utilisateur,
 * d'un cursus et d'un achat, ainsi que l'accès aux pages pertinentes après connexion.
 *
 * @package App\Tests\Controller
 */
class PurchaseTest extends WebTestCase
{
    /**
     * @var User L'utilisateur utilisé pour les tests.
     */
    private $user;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser Le client utilisé pour effectuer les requêtes HTTP.
     */
    private $client;

    /**
     * @var EntityManagerInterface Le gestionnaire d'entités utilisé pour manipuler la base de données.
     */
    private $entityManager;

    /**
     * Configure l'environnement de test.
     *
     * Cette méthode initialise le client, le gestionnaire d'entités, et crée un utilisateur,
     * un cursus, ainsi qu'un achat si nécessaire.
     */
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

    /**
     * Teste le processus d'achat.
     *
     * Cette méthode vérifie si l'utilisateur peut se connecter, accéder à son panier,
     * et si le processus d'achat fonctionne correctement.
     */
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