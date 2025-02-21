<?php
// src/Controller/InstructeurRegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\InstructeurRegistrationFormType;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class InstructeurRegistrationController extends AbstractController
{
    #[Route('/dashboard/ajoutInstructeur', name: 'app_register_instructeur')]
    public function register(
        Request $request,   
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        PasswordGenerator $passwordGenerator,
        MailerInterface $mailer,
        UserRepository $userRepository // Injecter le repository User
    ): Response {
        // Créer une nouvelle instance de User
        $user = new User();

        // Créer le formulaire d'inscription pour l'instructeur
        $form = $this->createForm(InstructeurRegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Traiter le formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email existe déjà
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if ($existingUser) {
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                return $this->redirectToRoute('app_register_instructeur');
            }

            // Générer un mot de passe aléatoire
            $plainPassword = $passwordGenerator->generateRandomPassword();

            // Encoder le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            // Attribuer le rôle ROLE_INSTRUCTEUR
            $user->setRoles(['ROLE_INSTRUCTEUR']);

            // Enregistrer l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer un email avec les informations de connexion
            $email = (new TemplatedEmail())
            ->from(new Address('mailer@domain.de', 'mailer bot'))
                ->to($user->getEmail())
                ->subject('Vos informations de connexion')
                ->htmlTemplate('email/instructeur_registration.html.twig')
                ->context(
                    [
                        'userEmail' => $user->getEmail(),
                        'password' => $plainPassword,
                        'firstName' => $user->getFirstName(),
                        'lastName' => $user->getLastName(),
                    ]
                );

            $mailer->send($email);

            

            // Ajouter un message flash et rediriger
            $this->addFlash('success', 'Compte instructeur créé avec succès. Un email a été envoyé avec les informations de connexion.');
            return $this->redirectToRoute('app_inst'); // Rediriger vers la liste des instructeurs
        }

        // Afficher le formulaire d'inscription
        return $this->render('dashboard/ajoutInstructeur.html.twig', [
            'instructeurRegistrationForm' => $form->createView(),
        ]);
    }
}