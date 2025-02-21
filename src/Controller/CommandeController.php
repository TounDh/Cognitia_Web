<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Paiement;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
