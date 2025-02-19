<?php


namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\InstructeurRegistrationFormType;

class InstructeurController extends AbstractController
{
    #[Route('/dashboard/inst', name: 'app_inst')]
    public function inst(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs avec le rôle ROLE_INSTRUCTEUR
        $instructeurs = $userRepository->findByRole('ROLE_INSTRUCTEUR');
    
        // Passer la variable "instructeurs" au template
        return $this->render('dashboard/inst.html.twig', [
            'instructeurs' => $instructeurs,
        ]);
    }

    #[Route('/admin/instructeur/{id}', name: 'app_instructeur_show', methods: ['GET'])]
    public function showInstructeur(User $instructeur): Response
    {
        return $this->render('instructeur/show.html.twig', [
            'instructeur' => $instructeur,
        ]);
    }

    #[Route('/admin/instructeur/{id}/edit', name: 'app_instructeur_edit', methods: ['GET', 'POST'])]
    public function editInstructeur(Request $request, User $instructeur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstructeurRegistrationFormType::class, $instructeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'instructeur a été mis à jour avec succès.');
            return $this->redirectToRoute('app_instructeurs_list');
        }

        return $this->render('instructeur/edit.html.twig', [
            'instructeur' => $instructeur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/instructeur/{id}/delete', name: 'app_instructeur_delete', methods: ['POST'])]
    public function deleteInstructeur(Request $request, User $instructeur, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $instructeur->getId(), $request->request->get('_token'))) {
            // Supprimer l'instructeur de la base de données
            $entityManager->remove($instructeur);
            $entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'L\'instructeur a été supprimé avec succès.');
        } else {
            // Ajouter un message d'erreur si le token CSRF est invalide
            $this->addFlash('error', 'Token CSRF invalide, suppression annulée.');
        }

        // Rediriger vers la liste des instructeurs
        return $this->redirectToRoute('app_instructeurs_list');
    }
}