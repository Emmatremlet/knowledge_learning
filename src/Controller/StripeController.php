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
            $successUrl = $this->generateUrl('user_lessons', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $cancelUrl = $this->generateUrl('cart_index', [], UrlGeneratorInterface::ABSOLUTE_URL);

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

    // #[Route('/checkout/success', name: 'stripe_success', methods: ['GET'])]
    // public function success(): Response
    // {
    //     $this->addFlash('success', 'Votre paiement a été effectué avec succès !');
    //     return $this->redirectToRoute('user_lessons');
    // }

    // #[Route('/checkout/cancel', name: 'stripe_cancel', methods: ['GET'])]
    // public function cancel(): Response
    // {
    //     $this->addFlash('danger', 'Votre paiement a été annulé.');
    //     return $this->redirectToRoute('cart_index');
    // }
}