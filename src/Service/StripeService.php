<?php

namespace App\Service;

use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct(string $secretKey)
    {
        if (!$secretKey) {
            throw new \RuntimeException('La clé secrète Stripe n\'est pas définie.');
        }

        $this->stripe = new StripeClient($secretKey);
    }

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