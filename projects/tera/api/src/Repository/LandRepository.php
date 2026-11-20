<?php

namespace App\Repository;

use App\Entity\Land;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Land>
 */
class LandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Land::class);
    }

    /**
     * @return Paginator<Land>
     */
    public function findLookingForMembers(int $page = 1, int $itemsPerPage = 30): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('l')
                ->innerJoin('l.landSetting', 'ls', Join::WITH, 'ls.lookingForMember = true')
                ->addCriteria(
                    Criteria::create()
                        ->setFirstResult(($page - 1) * $itemsPerPage)
                        ->setMaxResults($itemsPerPage)
                )
        );
    }
}
