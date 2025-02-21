<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CoursRepository;


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
    #[IsGranted('ROLE_ADMIN')]
    public function cours(CoursRepository $coursRepository): Response
    {
        $courses = $coursRepository->findAll();
        
        return $this->render('dashboard/cours.html.twig', [
            'courses' => $courses,
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
    #[Route('/dashboard/ajoutEv', name: 'app_ajoutEv')] 
    public function ajoutEv(): Response
    {
        return $this->render('dashboard/ajoutevent.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutApprenant', name: 'app_ajoutApprenant')]
    public function ajoutApprenant(): Response
    {
        return $this->render('dashboard/ajoutApprenant.html.twig', [
        ]);
    }

    #[Route('/dashboard/ajoutInstructeur', name: 'app_register_instructeur')]
    public function ajoutInstructeur(): Response
    {
        return $this->render('registration/instructeur_register.html.twig', [
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


    
    #[Route('/dashboard', name:'dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(): Response
    {
        // Seuls les utilisateurs avec le rôle ROLE_ADMIN peuvent accéder ici
        return $this->render('dashboard.html.twig');
    }

}
