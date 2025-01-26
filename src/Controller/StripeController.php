<?php

namespace App\Controller;

use App\Service\StripeService;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class StripeController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    #[Route('/checkout', name: 'stripe_checkout', methods: ['POST'])]
    public function checkout(Request $request, StripeService $stripeService, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié.'], 401);
        }

        $cart = $user->getPurchases();
        if (!$cart || count($cart) === 0) {
            return $this->json(['error' => 'Votre panier est vide.'], 400);
        }

        $lineItems = [];
        foreach ($cart as $purchase) {
            $productName = null;
            $price = null;

            if ($purchase->getLesson()) {
                $productName = $purchase->getLesson()->getName();
                $price = $purchase->getLesson()->getPrice();
            }
            elseif ($purchase->getCursus()) {
                $productName = $purchase->getCursus()->getName();
                $price = $purchase->getCursus()->getPrice();
            }


            if (!$productName || $price <= 0) {
                return new JsonResponse(['error' => 'Produit invalide détecté dans le panier.'], 400);
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $productName,
                    ],
                    'unit_amount' => $price * 100,
                ],
                'quantity' => 1,
            ];
        }

        try {
            $successUrl = $this->generateUrl('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $cancelUrl = $this->generateUrl('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

            $sessionId = $stripeService->createCheckoutSession($lineItems, $successUrl, $cancelUrl);

            if (!$sessionId) {
                return new JsonResponse(['error' => 'Session Stripe non créée.'], 500);
            }
            return new JsonResponse(['id' => $sessionId]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur Stripe : ' . $e->getMessage());
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/checkout/success', name: 'stripe_success', methods: ['GET'])]
    public function success(): RedirectResponse
    {
        $this->addFlash('success', 'Votre paiement a été effectué avec succès !');
        return $this->redirectToRoute('user_lessons');
    }

    #[Route('/checkout/cancel', name: 'stripe_cancel', methods: ['GET'])]
    public function cancel(): RedirectResponse
    {
        $this->addFlash('danger', 'Votre paiement a été annulé.');
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function handleWebhook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payload = $request->getContent();
        $signature = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        // Gérer les événements Stripe
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Logique pour marquer les cours comme achetés
            $this->processPurchase($session, $entityManager);
        }

        return new Response('Webhook handled', 200);
    }

    private function processPurchase(object $session, EntityManagerInterface $entityManager): void
    {
        $userEmail = $session->customer_details.email;
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        if (!$user) {
            throw new \RuntimeException('Utilisateur non trouvé pour l\'email : ' . $userEmail);
        }

        $cart = $entityManager->getRepository(Purchase::class)->findBy([
            'user' => $user,
            'status' => 'pending',
        ]);

        foreach ($cart as $purchase) {
            $purchase->setStatus('completed');
            $entityManager->persist($purchase);
        }

        $entityManager->flush();
    }
}