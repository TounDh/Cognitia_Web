<?php

namespace App\Repository;

use App\Entity\UserProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserProgress>
 */
class UserProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProgress::class);
    }

    /**
     * Find UserProgress records by user and module.
     */
    public function findByUserAndModule($user, $module): ?UserProgress
    {
        return $this->createQueryBuilder('up')
            ->andWhere('up.user = :user')
            ->andWhere('up.module = :module')
            ->setParameter('user', $user)
            ->setParameter('module', $module)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all UserProgress records for a specific user.
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('up')
            ->andWhere('up.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all UserProgress records for a specific module.
     */
    public function findByModule($module): array
    {
        return $this->createQueryBuilder('up')
            ->andWhere('up.module = :module')
            ->setParameter('module', $module)
            ->getQuery()
            ->getResult();
    }
}