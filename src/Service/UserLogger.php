<?php
// src/Service/UserLogger.php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserLogger
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    // src/Service/UserLogger.php

public function log(?User $user, string $action, ?string $details = null): void
{
    // Vérifier si l'utilisateur est null
    if ($user === null && $action !== 'login_failed') {
        throw new \InvalidArgumentException('User cannot be null for this action.');
    }

    // Récupérer la requête actuelle
    $request = $this->requestStack->getCurrentRequest();

    // Récupérer l'adresse IP de l'utilisateur
    $ipAddress = $request ? $request->getClientIp() : 'Unknown IP';

    // Récupérer les informations de l'appareil (navigateur et OS)
    $userAgent = $request ? $request->headers->get('User-Agent') : 'Unknown Device';

    // Créer un nouveau log
    $log = new UserLog();
    $log->setUser($user); // Cela fonctionnera si setUser accepte null
    $log->setAction($action);
    $log->setDetails(sprintf(
        "IP: %s, Appareil: %s, Détails: %s",
        $ipAddress,
        $userAgent,
        $details ?? 'Aucun détail supplémentaire'
    ));
    $log->setCreatedAt(new \DateTime());

    // Enregistrer le log dans la base de données
    $this->entityManager->persist($log);
    $this->entityManager->flush();

    // Log temporaire pour vérification
    error_log(sprintf('Log enregistré: Action: %s, User: %s', $action, $user ? $user->getFirstName() : 'Anonymous'));
}
}