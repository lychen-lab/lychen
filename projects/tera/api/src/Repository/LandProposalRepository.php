<?php

namespace App\Repository;

use App\Entity\Land;
use App\Entity\LandProposal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandProposal>
 */
class LandProposalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandProposal::class);
    }

    public function hasExistingStateForLand(Land $land,
        string $state,
        ?string $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.land = :land')
            ->andWhere('e.state = :state')
            ->setParameter('land', $land)
            ->setParameter('state', $state);

        if ($excludeId) {
            $qb->andWhere('e.id != :id')
                ->setParameter('id', $excludeId);
        }

        $result = $qb->getQuery()->getOneOrNullResult();

        return null !== $result;
    }
}
