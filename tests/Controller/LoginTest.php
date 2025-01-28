<?php

namespace App\Tests\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LoginTest
 *
 * Cette classe teste les fonctionnalités de la page de connexion, notamment l'accès à la page,
 * la connexion avec des identifiants valides et non valides.
 *
 * @package App\Tests\Security
 */
class LoginTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser Le client utilisé pour effectuer les requêtes HTTP.
     */
    private $client;

    /**
     * Configure l'environnement de test.
     *
     * Cette méthode initialise le client et s'assure qu'un utilisateur de test existe dans la base de données
     * avec des identifiants valides pour les tests de connexion.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);

        // Vérifie si l'utilisateur existe déjà, sinon le crée
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user@example.com']);
        if (!$user) {
            $user = new User();
            $user->setName('Test User');
            $user->setEmail('user@example.com');
            $user->setPassword(password_hash('password123', PASSWORD_BCRYPT));
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }

    /**
     * Teste si la page de connexion est accessible et affiche le texte attendu.
     */
    public function testLoginPageIsSuccessful(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Veuillez vous connecter');
    }

    /**
     * Teste la connexion avec des identifiants valides.
     *
     * Vérifie que l'utilisateur est redirigé vers la page d'accueil après une connexion réussie.
     */
    public function testLoginWithValidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['email'] = 'user@example.com';
        $form['password'] = 'password123';

        $this->client->submit($form);

        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Nos cours phares');
    }

    /**
     * Teste la connexion avec des identifiants invalides.
     *
     * Vérifie que l'utilisateur est redirigé vers la page de connexion après un échec.
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['email'] = 'invalid@example.com';
        $form['password'] = 'wrongpassword';

        $this->client->submit($form);

        $response = $this->client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
    }
}