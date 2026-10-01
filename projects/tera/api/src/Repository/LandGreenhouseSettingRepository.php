<?php

namespace App\Repository;

use App\Entity\LandGreenhouseSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandGreenhouseSetting>
 */
class LandGreenhouseSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandGreenhouseSetting::class);
    }
}
