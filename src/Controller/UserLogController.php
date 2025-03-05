<?php
// src/Controller/UserLogController.php

namespace App\Controller;

use App\Repository\UserLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserLogController extends AbstractController
{
    #[Route('/dashboard/user/logs', name: 'app_user_logs')]
    public function index(UserLogRepository $userLogRepository): Response
    {
        // Récupérer tous les logs avec une jointure sur l'utilisateur
        $logs = $userLogRepository->createQueryBuilder('l')
            ->leftJoin('l.user', 'u') // Jointure avec l'entité User
            ->addSelect('u') // Sélectionner l'utilisateur
            ->orderBy('l.createdAt', 'DESC') // Trier par date de création décroissante
            ->getQuery()
            ->getResult();

        return $this->render('user_log/index.html.twig', [
            'logs' => $logs,
        ]);
    }
}