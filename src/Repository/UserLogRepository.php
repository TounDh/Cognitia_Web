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

    public function findAllUsers(): array
    {
        return $this->createQueryBuilder('l')
            ->select('DISTINCT u.id, u.email') // Sélectionner l'ID et l'email de l'utilisateur
            ->leftJoin('l.user', 'u') // Jointure avec l'entité User
            ->orderBy('u.email', 'ASC') // Trier par email
            ->getQuery()
            ->getResult();
    }

}