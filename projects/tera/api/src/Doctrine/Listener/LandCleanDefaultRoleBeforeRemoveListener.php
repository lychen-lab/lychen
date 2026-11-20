<?php

namespace App\Doctrine\Listener;

use App\Entity\Land;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, entity: Land::class)]
final readonly class LandCleanDefaultRoleBeforeRemoveListener
{
    public function __invoke(Land $land, PreRemoveEventArgs $event): void
    {
        $land->setDefaultRole(null);
        $event->getObjectManager()->flush();
    }
}
