<?php

namespace App\Repository;

use App\Entity\LandMember;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandMember>
 */
class LandMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandMember::class);
    }

    /**
     * Finds the IDs of all Lands a specific person is a member of.
     *
     * @return array<int> an array of Land IDs
     */
    public function findPersonLandIds(Person $person): array
    {
        $qb = $this->createQueryBuilder('lm'); // lm = alias for LandMember

        $qb->select('IDENTITY(lm.land)') // Select the ID of the associated land
        ->where('lm.person = :person')
            ->setParameter('person', $person);

        $result = $qb->getQuery()->getScalarResult();

        // Extract the IDs from the result array
        return array_map('current', $result);
    }
}
