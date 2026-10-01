<?php

namespace App\Doctrine\Listener;

use App\Entity\LandRequest;
use App\Entity\Person;
use App\Entity\PersonApiKey;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEntityListener(event: Events::prePersist, entity: LandRequest::class)]
final readonly class LandRequestLinkPersonListener
{
    public function __construct(private Security $security, private LoggerInterface $logger)
    {
    }

    public function __invoke(LandRequest $landRequest, LifecycleEventArgs $event): void
    {
        if (!$person = $landRequest->getPerson()) {
            $person = $this->security->getUser();
            if ($person instanceof PersonApiKey) {
                $person = $person->getPerson();
            }
        }

        if (!$person instanceof Person) {
            return;
        }

        /* @var UserInterface&Person $person */

        $landRequest->setPerson($person);

        $this->logger->info('User '.$person->getUserIdentifier().' wants to created a land request');
    }
}
