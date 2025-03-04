<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Paiement;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/commande')]
final class CommandeController extends AbstractController
{
    #[Route(name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

















//l'affichage

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        $panier = $commande->getPanier();
        $user = $panier ? $panier->getUser() : null;
        $cours = $panier ? $panier->getCours() : []; 



        $sum = array_reduce($commande->getPanier()->getCours()->toArray(), function ($carry, $cours) {
            return $carry + $cours->getPrix();
        }, 0);
        
        $tax = $sum * 0.10; 
        $total = $sum + $tax;


        return $this->render('commande/index.html.twig', [
        'commande' => $commande,
        'panier' => $panier,
        'user' => $user,
        'cours' => $cours,
        'subtotal' => $sum,
        'tax' => $tax,
        'total' => $total,
        ]);
    }























//STRIPE
//STRIPE
//STRIPE
//STRIPE
    #[Route('/pay/{id}', name: 'pay', methods: ['POST'])]
    public function checkout(Request $request, Commande $commande): JsonResponse
    {
        try {
            // Set Stripe API key
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            
            // Get panier and calculate amount
            $panier = $commande->getPanier();
            $sum = 0;
            foreach ($panier->getCours() as $cours) {
                $sum += $cours->getPrix();
            }
            
            $tax = $sum * 0.10;
            $total = $sum + $tax;
            $amount = (int)round($total * 100);
            
            // Create a more basic checkout session without success/cancel URLs
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Course Payment',
                            'description' => 'Payment for order #' . $commande->getId(),
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                // Store the return path as metadata instead of using success/cancel URLs
                'metadata' => [
                    'commande_id' => $commande->getId(),
                ],
                // Use a single return URL that can handle both success and cancel cases
                'success_url' => $request->getSchemeAndHttpHost() . $this->generateUrl('payment_success', ['id' => $commande->getId()]), // This is a placeholder
                'cancel_url' => 'https://example.com/cancel',   // This is a placeholder
            ]);
            
            // Return the checkout URL
            return new JsonResponse(['url' => $session->url]);
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    #[Route('/payment/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(Request $request): Response
    {
        // This would be used with a properly configured webhook
        // We'll skip webhook implementation for now
        return new Response('', 200);
    }
    
    #[Route('/payment/check/{session_id}', name: 'check_payment')]
    public function checkPayment(string $session_id): Response
    {
        try {
            Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $session = Session::retrieve($session_id);
            
            // Based on session status, redirect to success or cancel pages
            if ($session->payment_status === 'paid') {
                return $this->redirectToRoute('payment_success');
            } else {
                return $this->redirectToRoute('payment_cancel');
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('payment_cancel');
        }
    }
    
    #[Route('/payment/success/{id}', name: 'payment_success')]
    public function paymentSuccess(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setStatut('paid');
        $entityManager->persist($commande);
        $entityManager->flush();
        
        // Add flash message to notify user
        $this->addFlash('success', 'Payment successful! Your order has been confirmed.');
        
        // Redirect to commande index page
        
        return $this->redirectToRoute('app_commande_show', [
            'id' => $commande->getId()
        ]);
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('commande/payment_cancel.html.twig');
    }
//STRIPE
//STRIPE
//STRIPE
//STRIPE


























    //paiement creation
    #[Route('/commande/{id}/pay', name: 'app_paiement_create', methods: ['POST'])]
    public function createPaiement(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $panier = $commande->getPanier();
    
        if (!$panier) {
            throw $this->createNotFoundException('No cart associated with this order.');
        }
    
        $sum = array_reduce($panier->getCours()->toArray(), function ($carry, $cours) {
            return $carry + $cours->getPrix();
        }, 0);
    
        $tax = $sum * 0.10;
        $total = $sum + $tax;
    
        $paiement = new Paiement();
        $paiement->setCommande($commande);
        $paiement->setDatePaiement(new \DateTime());
        $paiement->setMontant($total); // Use calculated total
        $paiement->setMethode('Pending');
        $paiement->setCardHolder('');
        $paiement->setCardNumber('');
        $paiement->setExpiryDate('');
        $paiement->setCvv('');

    
        $entityManager->persist($paiement);
        $entityManager->flush();
    
        return $this->redirectToRoute('payment', ['id' => $commande->getId()]);
    }





    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }






    #[Route('/{id}/delete', name: 'app_commande_delete', methods: ['GET'])]
public function delete(Commande $commande, EntityManagerInterface $entityManager): Response
{
    $panier = $commande->getPanier();

    if ($panier) {
        $panier->setStatut('in progress..');
        $entityManager->persist($panier); 
    }


    $entityManager->remove($commande);
    $entityManager->flush();

    return $this->redirectToRoute('app_panier_show', ['id' => $panier->getId()]);
}


}
