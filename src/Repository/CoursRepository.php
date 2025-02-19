<?php


namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    // Add the save method here
    public function save(Cours $entity, bool $flush = false): void
    {
        $this->_em->persist($entity); // Persist the entity

        if ($flush) {
            $this->_em->flush(); // If flush is true, save the entity to the database
        }
    }

    // Optionally, add a remove method as well if you want
    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->_em->remove($entity); // Remove the entity

        if ($flush) {
            $this->_em->flush(); // If flush is true, delete the entity from the database
        }
    }
}
