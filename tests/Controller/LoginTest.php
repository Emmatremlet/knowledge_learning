<?php

namespace App\Tests\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;


class LoginTest extends WebTestCase
{
    private $client;
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $entityManager =  $this->client->getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user@example.com']);

        if (!$user) {
            $user = new User();
            $user->setName('Test User');
            $user->setEmail('user@example.com');
            $user->setPassword(password_hash('password123', PASSWORD_BCRYPT)); // Ensure password matches
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }

    public function testLoginPageIsSuccessful(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Veuillez vous connecter');
    }

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