<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface; 
use App\Service\UserLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class SecurityController extends AbstractController
{
    private UserLogger $userLogger;
    private GoogleAuthenticatorInterface $googleAuthenticator;
    private EntityManagerInterface $entityManager;
    private $tokenStorage;



    public function __construct(GoogleAuthenticatorInterface $googleAuthenticator,UserLogger $userLogger,EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage
    )
    {
        $this->googleAuthenticator = $googleAuthenticator;
        $this->userLogger = $userLogger;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;



    }

    


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Si l'utilisateur est déjà connecté, redirigez-le
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->render('dashboard/index.html.twig');
            }
            return $this->redirectToRoute('app_cours');
        }

        // Enregistrer la tentative de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            // Enregistrer le log de tentative de connexion échouée
            $this->userLogger->log(
                null, // Pas d'utilisateur si la connexion échoue
                'login_failed',
                sprintf('IP: %s, Username: %s', $request->getClientIp(), $lastUsername)
            );

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('error', 'Adresse e-mail ou mot de passe incorrect.');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }




    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(RequestStack $requestStack): void
    {
        // Enregistrer la déconnexion
        $user = $this->getUser();
        if ($user) {
            $request = $requestStack->getCurrentRequest();
            $this->userLogger->log(
                $user,
                'logout',
                sprintf('IP: %s', $request ? $request->getClientIp() : 'Unknown IP')
            );
        }
    
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/2fa', name: 'app_2fa')]
    public function twoFactorAuth(Request $request): Response
    {
        return $this->render('security/2fa_form.html.twig');
    }

    #[Route(path: '/2fa_check', name: 'app_2fa_check')]
    public function twoFactorCheck(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        // Récupérer le code soumis par l'utilisateur
        $authCode = $request->request->get('_auth_code');
    
        // Vérifier si le code est valide
        if ($this->googleAuthenticator->checkCode($user, $authCode)) {
            // Code correct : créer un nouveau token et le définir comme authentifié
            $token = new UsernamePasswordToken(
                $user,                          // L'utilisateur
                'main',                         // Le nom du firewall
                $user->getRoles()                // Les rôles de l'utilisateur
            );
            $tokenStorage->setToken($token);     // Définir le nouveau token
    
            // Rediriger l'utilisateur vers la page d'accueil ou une autre page
            return $this->redirectToRoute('app_cours');
        } else {
            // Code incorrect : afficher un message d'erreur
            $this->addFlash('error', 'Le code de vérification est incorrect. Veuillez réessayer.');
            return $this->redirectToRoute('app_2fa'); // Rediriger vers la page 2FA
        }
    }






    #[Route(path: '/enable-2fa', name: 'app_enable_2fa')]
    public function enable2fa(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getGoogleAuthenticatorSecret() === null) {
            $secret = $this->googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $qrCodeContent = $this->googleAuthenticator->getQRContent($user);

        // ✅ Corrected Code (v6 format)
        $qrCode = new QrCode(
            $qrCodeContent,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::High, // ✅ Use constant
            300, // size
            10,  // margin
            RoundBlockSizeMode::Margin // ✅ Use the correct class
        );

        // ✅ Use PngWriter to generate the QR code
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Convert QR code to base64 for displaying in Twig
        $qrCodeImage = base64_encode($result->getString());

        return $this->render('security/enable_2fa.html.twig', [
            'qrCodeImage' => $qrCodeImage,
            'secret' => $user->getGoogleAuthenticatorSecret(),

        ]);
    }

    #[Route('/disable-2fa', name: 'app_disable_2fa')]
    public function disable2fa(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Désactiver la 2FA en supprimant le secret
        $user->setGoogleAuthenticatorSecret(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Rediriger vers la page de modification du profil
        return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
    }

}