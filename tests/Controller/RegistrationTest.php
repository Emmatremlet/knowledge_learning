<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    public function testUserRegistration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_form[name]' => 'Test User',
            'registration_form[email]' => 'testuser2@example.com',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[agreeTerms]' => true,
        ]);

        $client->submit($form);
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect('/login'));
        $this->assertEquals(302, $response->getStatusCode());
        
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }
}