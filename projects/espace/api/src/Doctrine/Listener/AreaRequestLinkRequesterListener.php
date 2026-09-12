<?php

namespace App\Doctrine\Listener;

use App\Entity\AreaRequest;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: AreaRequest::class)]
final readonly class AreaRequestLinkRequesterListener
{
    public function __construct(private Security $security)
    {
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function __invoke(AreaRequest $areaRequest, LifecycleEventArgs $event): void
    {
        if ($areaRequest->getRequester()) {
            return;
        }

        $requester = $this->security->getUser();
        if (!$requester instanceof Person) {
            return;
        }

        $areaRequest->setRequester($requester);
    }
}
