<?php

namespace App\Repository;

use App\Entity\LandRequest;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandRequest>
 */
class LandRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandRequest::class);
    }

    public function hasExistingStateForPerson(Person $person, string $state, ?string $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.person = :person')
            ->andWhere('e.state = :state')
            ->setParameter('person', $person)
            ->setParameter('state', $state);

        if ($excludeId) {
            $qb->andWhere('e.id != :id')
                ->setParameter('id', $excludeId);
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return null !== $result;
    }
}
