<?php

namespace App\Doctrine\Listener;

use App\Entity\Land;
use App\Entity\LandRole;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Land::class)]
final readonly class LandInitDefaultRolesListener
{
    public function __invoke(Land $land): void
    {
        $roleCultivator = (new LandRole())
            ->setName('Cultivateur')
            ->setPermissions([]);

        $land->addLandRole($roleCultivator);
        $land->setDefaultRole($roleCultivator);
    }
}
