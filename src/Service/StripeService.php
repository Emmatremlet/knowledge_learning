<?php

namespace App\Service;

use Stripe\StripeClient;

/**
 * Class StripeService
 *
 * Ce service gère les interactions avec l'API Stripe, notamment la création de sessions de paiement.
 *
 * @package App\Service
 */
class StripeService
{
    /**
     * @var StripeClient Le client Stripe utilisé pour effectuer les appels API.
     */
    private StripeClient $stripe;

    /**
     * StripeService constructor.
     *
     * Initialise le client Stripe avec la clé secrète fournie.
     *
     * @param string $secretKey La clé secrète Stripe nécessaire pour authentifier les appels API.
     * 
     * @throws \RuntimeException Si la clé secrète n'est pas définie.
     */
    public function __construct(string $secretKey)
    {
        if (!$secretKey) {
            throw new \RuntimeException('La clé secrète Stripe n\'est pas définie.');
        }

        $this->stripe = new StripeClient($secretKey);
    }

    /**
     * Crée une session de paiement Stripe.
     *
     * Cette méthode configure une session de paiement en utilisant les éléments de ligne fournis
     * et les URL de redirection pour les cas de succès ou d'annulation.
     *
     * @param array $lineItems Les éléments de la commande, comprenant les détails des articles.
     * @param string $successUrl L'URL de redirection en cas de succès du paiement.
     * @param string $cancelUrl L'URL de redirection en cas d'annulation du paiement.
     *
     * @return string L'ID de la session de paiement créée.
     * 
     * @throws \RuntimeException En cas d'erreur lors de la création de la session.
     */
    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl): string
    {
        try {
            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl, 
                'cancel_url' => $cancelUrl,
            ]);
            return $session->id;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \RuntimeException('Erreur lors de la création de la session Stripe : ' . $e->getMessage());
        }
    }
}