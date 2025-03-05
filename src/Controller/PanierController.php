<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Cours;
use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\CoursRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
final class PanierController extends AbstractController
{
    #[Route( name: 'app_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        return $this->render('panier/show.html.twig', [
            'paniers' => $panierRepository->findAll(),
        ]);
    }





    #[Route('/new', name: 'app_panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

































    #[Route('/add-to-cart/{coursId}', name: 'app_panier_add_to_cart', methods: ['POST'])]
    public function addToCart(int $coursId, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $cours = $entityManager->getRepository(Cours::class)->find($coursId);
    
        if (!$cours) {
            throw $this->createNotFoundException('Course not found');
        }
    
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $user,'statut' => 'in progress..']);
    
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $panier->setDateCreation(new \DateTime());
            $panier->setStatut('in progress..');
    
            $entityManager->persist($panier);
        }
    
        $panier->addCour($cours); 
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_panier_show', ['id' => $panier->getId()]);
    }








   
    






















    #[Route('/{id}', name: 'app_panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'cours' => $panier->getCours(),
        ]);
    }









































    //el checkout
    #[Route('/checkout/{id}', name: 'app_panier_checkout', methods: ['POST'])]
public function checkout(Panier $panier, EntityManagerInterface $entityManager, Request $request): Response
{
   
    $submittedToken = $request->request->get('_token');
    if (!$this->isCsrfTokenValid('checkout_' . $panier->getId(), $submittedToken)) {
        throw $this->createAccessDeniedException('Invalid CSRF token');
    }

    $panier->setStatut('confirmed');

    $commande = new Commande();
    $commande->setPanier($panier);
    $commande->setDateAchat(new \DateTime()); 
    $commande->setStatut('unpaid'); 

    $entityManager->persist($commande);

    $entityManager->flush();

    return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
}














//tan7it el cours

#[Route('/panier/remove-cours/{coursId}', name: 'app_panier_remove_cours', methods: ['POST'])]
public function removeCoursFromPanier(int $coursId, EntityManagerInterface $entityManager, Security $security): Response
{
    // Récupérer l'utilisateur connecté
    $user = $security->getUser();

    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Récupérer le cours à supprimer
    $cours = $entityManager->getRepository(Cours::class)->find($coursId);

    // Vérifier si le cours existe
    if (!$cours) {
        throw $this->createNotFoundException('Course not found');
    }

    // Récupérer le panier de l'utilisateur
    $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $user]);

    // Vérifier si le panier existe
    if ($panier) {
        // Supprimer le cours du panier
        $panier->removeCour($cours);

        // Enregistrer les modifications en base de données
        $entityManager->flush();
    }

    // Rediriger vers la page du panier
    return $this->redirectToRoute('app_panier_show', ['id' => $panier->getId()]);
}



























    #[Route('/{id}/edit', name: 'app_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }



















 
//delete panier

#[Route('/{id}', name: 'app_panier_delete', methods: ['POST'])]
public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($panier);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_panier_index');
}












}
