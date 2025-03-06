<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByFiltersAndSort(?string $search, ?DateTime $date, ?string $type, ?string $sort = 'titre', string $direction = 'ASC'): array
{
    $qb = $this->createQueryBuilder('e');

    if ($search) {
        $qb->andWhere('e.titre LIKE :search OR e.description LIKE :search OR e.lieu LIKE :search')
           ->setParameter('search', '%' . $search . '%');
    }

    $sort = $sort ?? 'titre';
    $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
    $qb->orderBy('e.' . $sort, $direction);

    return $qb->getQuery()->getResult();
}
}