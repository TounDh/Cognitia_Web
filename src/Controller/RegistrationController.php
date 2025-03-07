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
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Notifier\TexterInterface; // Add this import



class RegistrationController extends AbstractController
{
    private TexterInterface $texter;

    public function __construct(TexterInterface $texter)
    {
        $this->texter = $texter;
    }

    #[Route('/register/apprenant', name: 'app_register_apprenant')]
public function registerApprenant(
    
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager,
    SessionInterface $session
): Response {
    // Rediriger l'utilisateur s'il est déjà connecté
    if ($this->getUser()) {
        return $this->redirectToRoute('app_home');
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

        // Stocker l'ID de l'utilisateur dans la session pour la vérification
        $session->set('user_id_to_verify', $user->getId());

        // Rediriger vers la page de vérification du numéro de téléphone
        return $this->redirectToRoute('app_verify_phone');
    }

    // Afficher le formulaire d'inscription
    return $this->render('registration/register_apprenant.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}


#[Route('/verify-phone', name: 'app_verify_phone')]
public function verifyPhone(
    Request $request,
    EntityManagerInterface $entityManager,
    SessionInterface $session
): Response {
    // Récupérer l'ID de l'utilisateur depuis la session
    $userId = $session->get('user_id_to_verify');

    if (!$userId) {
        // Rediriger si l'ID de l'utilisateur n'est pas trouvé dans la session
        return $this->redirectToRoute('app_register_apprenant');
    }

    // Récupérer l'utilisateur depuis la base de données
    $user = $entityManager->getRepository(User::class)->find($userId);

    if (!$user) {
        // Rediriger si l'utilisateur n'est pas trouvé
        return $this->redirectToRoute('app_register_apprenant');
    }

    // Générer un code de vérification
    $verificationCode = $this->generateVerificationCode();

    // Envoyer le code par SMS
    $this->sendVerificationCode($user->getPhoneNumber(), $verificationCode, $this->texter);

    // Stocker le code dans la session
    $session->set('phone_verification_code', $verificationCode);

    // Afficher le formulaire de vérification
    return $this->render('registration/verify_phone.html.twig');
}




        public function generateVerificationCode(): string
        {
            return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }




    private function sendVerificationCode(string $phoneNumber, string $code, TexterInterface $texter): void
        {
            $sms = new SmsMessage(
                $phoneNumber,
                sprintf('Votre code de vérification est : %s', $code)
            );

            $texter->send($sms); // Use TexterInterface to send the SMS
        }



    #[Route('/validate-verification-code', name: 'app_validate_verification_code', methods: ['POST'])]
    public function validateVerificationCode(
        Request $request,
        SessionInterface $session,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer le code saisi par l'utilisateur
        $userCode = $request->request->get('verification_code');
    
        // Récupérer le code stocké dans la session
        $storedCode = $session->get('phone_verification_code');
    
        // Récupérer l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id_to_verify');
    
        if (!$userId || !$storedCode || $userCode !== $storedCode) {
            // Code invalide ou session expirée
            $this->addFlash('error', 'Code de vérification invalide.');
            return $this->redirectToRoute('app_verify_phone');
        }
    
        // Récupérer l'utilisateur depuis la base de données
        $user = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$user) {
            // Rediriger si l'utilisateur n'est pas trouvé
            return $this->redirectToRoute('app_register_apprenant');
        }
    
        // Marquer le numéro de téléphone comme vérifié
        $user->setIsPhoneVerified(true);
        $entityManager->flush();
    
        // Nettoyer la session
        $session->remove('phone_verification_code');
        $session->remove('user_id_to_verify');
    
        // Rediriger vers une page de succès
        return $this->redirectToRoute('app_login');
    }

}