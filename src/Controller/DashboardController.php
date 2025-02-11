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




}
