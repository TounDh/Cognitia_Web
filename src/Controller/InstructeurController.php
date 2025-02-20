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
use App\Form\RegistrationApprenantFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




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
            return $this->redirectToRoute('app_inst');
        }

        return $this->render('dashboard/modifInstructeur.html.twig', [
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
        return $this->redirectToRoute('app_inst');
    }

//apprenant management 
    #[Route('/dashboard/apprenant', name: 'app_apprenant')]
    public function apprenant(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs avec le rôle ROLE_APPRENANT
        $apprenants = $userRepository->findByRole('ROLE_APPRENANT');
    
        return $this->render('dashboard/apprenant.html.twig', [
            'apprenants' => $apprenants,
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

    #[Route('/dashboard/ajoutApprenant', name: 'app_ajoutApprenant')]
    public function ajouterApprenant(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $apprenant = new User();
        $form = $this->createForm(RegistrationApprenantFormType::class, $apprenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword(
                $apprenant,
                $form->get('plainPassword')->getData()
            );
            $apprenant->setPassword($hashedPassword);

            // Set the role to ROLE_APPRENANT
            $apprenant->setRoles(['ROLE_APPRENANT']);

            // Save the apprenant to the database
            $entityManager->persist($apprenant);
            $entityManager->flush();

            $this->addFlash('success', 'L\'apprenant a été ajouté avec succès.');
            return $this->redirectToRoute('app_apprenant');
        }

        return $this->render('dashboard/ajoutApprenant.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}