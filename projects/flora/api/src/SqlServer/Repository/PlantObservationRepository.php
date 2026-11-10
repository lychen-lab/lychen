<?php

namespace App\SqlServer\Repository;

use App\SqlServer\Entity\PlantObservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Bound to the `sqlserver` entity manager: the registry hands out the manager that
 * maps the class.
 *
 * @extends ServiceEntityRepository<PlantObservation>
 */
class PlantObservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlantObservation::class);
    }
}
