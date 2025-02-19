<?php
// src/Controller/ApprenantController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationApprenantFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register/apprenant', name: 'app_register_apprenant')]
    public function registerApprenant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Rediriger l'utilisateur s'il est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('display_dashboard');
        }
    
        // Créer une nouvelle instance de User
        $user = new User();
    
        // Créer le formulaire d'inscription pour l'apprenant
        $form = $this->createForm(RegistrationApprenantFormType::class, $user);
        $form->handleRequest($request);
    
        // Debug form errors
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                dump($error->getMessage());
            }
            dd('Form validation failed');
        }
    
        // Traiter le formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe en clair
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
    
           
    
            // Encoder le mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
    
            // Attribuer le rôle ROLE_APPRENANT
            $user->setRoles(['ROLE_APPRENANT']);
    
            // Enregistrer l'apprenant en base de données
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
    
        // Afficher le formulaire d'inscription
        return $this->render('registration/register_apprenant.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}