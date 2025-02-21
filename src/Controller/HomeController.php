<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig', [
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
        ]);
    }


    #[Route('/login2', name: 'app_login2')]
    public function login(): Response
    {
        return $this->render('home/login2.html.twig', [
        ]);
    }

    #[Route('/register2', name: 'app_register2')]
    public function register(): Response
    {
        return $this->render('home/register2.html.twig', [
        ]);
    }

    #[Route('/cours', name: 'app_cours')]
    public function cours(): Response
    {
        return $this->render('home/cours.html.twig', [
        ]);
    }


    #[Route('/event', name: 'app_event')]
    public function event(): Response
    {
        return $this->render('home/event.html.twig', [
        ]);
    }

    #[Route('/instructeur', name: 'app_instructeur')]
    public function instructeur(): Response
    {
        return $this->render('home/instructeur.html.twig', [
        ]);
    }

    #[Route('/resetpassword', name: 'app_resetpassword')]
    public function resetpassword(): Response
    {
        return $this->render('home/resetmdp.html.twig', [
        ]);
    }
  




}
