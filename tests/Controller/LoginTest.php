<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Veuillez vous connecter');

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/home');

        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Bienvenue sur la page d\'accueil');
    }
}