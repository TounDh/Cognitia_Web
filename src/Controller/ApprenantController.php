<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationApprenantFormType;

class ApprenantController extends AbstractController
{
    #[Route('/dashboard/apprenant', name: 'app_apprenant')]
    public function apprenant(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs avec le rôle ROLE_APPRENANT
        $apprenants = $userRepository->findByRole('ROLE_APPRENANT');
    
        return $this->render('dashboard/apprenant.html.twig', [
            'apprenants' => $apprenants,
        ]);
    }


    #[Route('/admin/apprenant/{id}', name: 'app_apprenant_show', methods: ['GET'])]
    public function showApprenant(User $apprenant): Response
    {
        return $this->render('apprenant/show.html.twig', [
            'apprenant' => $apprenant,
        ]);
    }

    #[Route('/admin/apprenant/{id}/edit', name: 'app_apprenant_edit', methods: ['GET', 'POST'])]
    public function editApprenant(Request $request, User $apprenant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistrationApprenantFormType::class, $apprenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'apprenant a été mis à jour avec succès.');
            return $this->redirectToRoute('app_apprenant');
        }

        return $this->render('dashboard/modifApprenant.html.twig', [
            'apprenant' => $apprenant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/apprenant/{id}/delete', name: 'app_apprenant_delete', methods: ['POST'])]
    public function deleteApprenant(Request $request, User $apprenant, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $apprenant->getId(), $request->request->get('_token'))) {
            // Supprimer l'apprenant de la base de données
            $entityManager->remove($apprenant);
            $entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'L\'apprenant a été supprimé avec succès.');
        } else {
            // Ajouter un message d'erreur si le token CSRF est invalide
            $this->addFlash('error', 'Token CSRF invalide, suppression annulée.');
        }

        // Rediriger vers la liste des apprenants
        return $this->redirectToRoute('app_apprenant');
    }
}