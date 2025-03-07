<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }


/** afficher seulement les quiz associe a l'instructeur connecte */
    public function findByInstructeur(User $instructeur): array
{
    return $this->createQueryBuilder('q')
        ->andWhere('q.instructeur = :instructeur')
        ->setParameter('instructeur', $instructeur)
        ->getQuery()
        ->getResult();
}


    //    /**
    //     * @return Quiz[] Returns an array of Quiz objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Quiz
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function paginateAll(int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('q')
            ->orderBy('q.id', 'ASC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    public function paginateQuery($queryBuilder, int $page, int $limit)
    {
        $query = $queryBuilder->getQuery();
        
        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
    

    
}
