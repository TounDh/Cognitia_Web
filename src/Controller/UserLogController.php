<?php
// src/Controller/UserLogController.php

namespace App\Controller;

use App\Repository\UserLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface; // Importez le paginator

class UserLogController extends AbstractController
{
    // src/Controller/UserLogController.php

// src/Controller/UserLogController.php

// src/Controller/UserLogController.php

#[Route('/dashboard/user/logs', name: 'app_user_logs')]
public function index(
    UserLogRepository $userLogRepository,
    PaginatorInterface $paginator,
    Request $request
): Response {
    // Récupérer les paramètres de filtrage
    $email = $request->query->get('email'); // Filtre par email

    // Créer une requête pour récupérer les logs avec une jointure sur l'utilisateur
    $queryBuilder = $userLogRepository->createQueryBuilder('l')
        ->leftJoin('l.user', 'u') // Jointure avec l'entité User
        ->addSelect('u') // Sélectionner l'utilisateur
        ->orderBy('l.createdAt', 'DESC'); // Trier par date de création décroissante

    // Appliquer le filtre par email si un email est fourni
    if ($email) {
        $queryBuilder->andWhere('u.email LIKE :email')
            ->setParameter('email', '%' . $email . '%'); // Recherche partielle
    }

    // Paginer les résultats
    $logs = $paginator->paginate(
        $queryBuilder->getQuery(), // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de page par défaut (1)
        $request->query->getInt('limit', 10) // Nombre d'éléments par page (dynamique)
    );

    // Récupérer la liste des utilisateurs pour le filtre
    $users = $userLogRepository->findAllUsers();

    return $this->render('user_log/index.html.twig', [
        'logs' => $logs,
        'users' => $users, // Passer la liste des utilisateurs au template
    ]);
}
}