<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RegistrationTest
 *
 * Cette classe teste le processus d'inscription d'un utilisateur, 
 * notamment l'accès à la page d'inscription, la soumission du formulaire
 * et la redirection après un enregistrement réussi.
 *
 * @package App\Tests\Controller
 */
class RegistrationTest extends WebTestCase
{
    /**
     * Teste le processus d'inscription d'un utilisateur.
     *
     * Cette méthode vérifie :
     * - L'accès à la page d'inscription.
     * - Le contenu attendu sur la page.
     * - La soumission d'un formulaire d'inscription valide.
     * - La redirection vers la page de connexion après une inscription réussie.
     * - La présence d'un message de succès.
     */
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