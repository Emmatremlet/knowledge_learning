<?php

namespace App\Controller;

use App\Service\StripeService;
use App\Entity\Purchase;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * StripeController
 * Gère les paiements Stripe et les webhooks associés.
 */
class StripeController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Crée une session de paiement Stripe pour les articles dans le panier de l'utilisateur.
     *
     * @Route("/checkout", name="stripe_checkout", methods={"POST"})
     * @param Request $request
     * @param StripeService $stripeService
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route("/checkout", name:"stripe_checkout", methods:"POST")]
    public function checkout(Request $request, StripeService $stripeService, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié.'], 401);
        }

        $cart = $user->getPurchases()->filter(function ($purchase) {
                return $purchase->getStatus() === 'pending';
        });

        if (!$cart || count($cart) === 0) {
            return $this->json(['error' => 'Votre panier est vide ou ne contient que des achats déjà traités.'], 400);
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

    /**
     * Affiche une page de succès après un paiement réussi.
     *
     * @Route("/checkout/success", name="stripe_success", methods={"GET"})
     * @return RedirectResponse
     */
    #[Route("/checkout/success", name:"stripe_success", methods:"GET")]
    public function success(): RedirectResponse
    {
        
        $this->addFlash('success', 'Votre paiement a été effectué avec succès !');
        return $this->redirectToRoute('user_lessons');
    }

    /**
     * Affiche une page d'annulation après un paiement annulé.
     *
     * @Route("/checkout/cancel", name="stripe_cancel", methods={"GET"})
     * @return RedirectResponse
     */
    #[Route("/checkout/cancel", name:"stripe_cancel", methods:"GET")]
    public function cancel(): RedirectResponse
    {
        $this->addFlash('danger', 'Votre paiement a été annulé.');
        return $this->redirectToRoute('cart_index');
    }

    /**
     * Gère les webhooks Stripe pour les événements liés aux paiements.
     *
     * @Route("/webhook", name="stripe_webhook", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route("/webhook", name:"stripe_webhook", methods:"POST")]
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

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $this->processPurchase($session, $entityManager);
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Marque les articles du panier comme achetés après un paiement réussi.
     *
     * @param object $session
     * @param EntityManagerInterface $entityManager
     * @return void
     */
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