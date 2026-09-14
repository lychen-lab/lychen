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

        // The authenticated user is not necessarily managed by this entity manager (the
        // firewall is stateless, so the token's user is never refreshed): link by reference.
        $entityManager = $event->getObjectManager();
        if (!$entityManager->contains($requester) && null !== $requester->getId()) {
            $requester = $entityManager->getReference(Person::class, $requester->getId());
        }

        $areaRequest->setRequester($requester);
    }
}
