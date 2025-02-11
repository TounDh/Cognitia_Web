<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
        ]);
    }

    #[Route('/dashboard/apprenant', name: 'app_apprenant')]
    public function apprenant(): Response
    {
        return $this->render('dashboard/apprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/inst', name: 'app_inst')]
    public function inst(): Response
    {
        return $this->render('dashboard/inst.html.twig', [
        ]);
    }


    #[Route('/dashboard/cours', name: 'app_coursManage')]
    public function cours(): Response
    {
        return $this->render('dashboard/cours.html.twig', [
        ]);
    }


    #[Route('/dashboard/module', name: 'app_module')]
    public function module(): Response
    {
        return $this->render('dashboard/module.html.twig', [
        ]);
    }


    
    #[Route('/dashboard/ev', name: 'app_ev')]
    public function ev(): Response
    {
        return $this->render('dashboard/ev.html.twig', [
        ]);
    }

    #[Route('/dashboard/commande', name: 'app_commande')]
    public function commande(): Response
    {
        return $this->render('dashboard/commande.html.twig', [
        ]);
    }

    #[Route('/dashboard/quiz', name: 'app_quiz')]
    public function quiz(): Response
    {
        return $this->render('dashboard/quiz.html.twig', [
        ]);
    }

    #[Route('/dashboard/contact', name: 'app_cont')]
    public function contact(): Response
    {
        return $this->render('dashboard/contact.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutApprenant', name: 'app_ajoutApprenant')]
    public function ajoutApprenant(): Response
    {
        return $this->render('dashboard/ajoutApprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutInstructeur', name: 'app_ajoutInstructeur')]
    public function ajoutInstructeur(): Response
    {
        return $this->render('dashboard/ajoutInstructeur.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutQuiz', name: 'app_ajoutQuiz')]
    public function ajoutQuiz(): Response
    {
        return $this->render('dashboard/ajoutQuiz.html.twig', [
        ]);
    }

    #[Route('/dashboard/modifApprenant', name: 'app_modifApprenant')]
    public function modifApprenant(): Response
    {
        return $this->render('dashboard/modifApprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/modifInstructeur', name: 'app_modifInstructeur')]
    public function modifInstructeur(): Response
    {
        return $this->render('dashboard/modifInstructeur.html.twig', [
        ]);
    }

    #[Route('/dashboard/modifQuiz', name: 'app_modifQuiz')]
    public function modifQuiz(): Response
    {
        return $this->render('dashboard/modifQuiz.html.twig', [
        ]);
    }

}
