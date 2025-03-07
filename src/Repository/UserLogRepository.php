<?php
// src/Repository/UserLogRepository.php

namespace App\Repository;

use App\Entity\UserLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLog::class);
    }

    // Ajoutez ici des méthodes personnalisées pour interagir avec la base de données
}