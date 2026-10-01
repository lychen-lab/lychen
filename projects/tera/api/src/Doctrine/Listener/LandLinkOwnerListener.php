<?php

namespace App\Doctrine\Listener;

use App\Entity\Land;
use App\Entity\LandMember;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEntityListener(event: Events::postPersist, entity: Land::class)]
final readonly class LandLinkOwnerListener
{
    public function __construct(private Security $security, private LoggerInterface $logger)
    {
    }

    public function __invoke(Land $land, LifecycleEventArgs $event): void
    {
        if (!$owner = $land->owner) {
            $owner = $this->security->getUser();
        }

        if (!$owner instanceof Person) {
            return;
        }

        /** @var UserInterface&Person $owner */
        $landMember = (new LandMember())
            ->setPerson($owner)
            ->setOwner(true);
        $land->addLandMember($landMember);

        $em = $event->getObjectManager();
        $em->persist($landMember);
        $em->flush();

        $this->logger->info('User '.$owner->getUserIdentifier().' has created a land', ['land_id' => $land->getId()]);
    }
}
