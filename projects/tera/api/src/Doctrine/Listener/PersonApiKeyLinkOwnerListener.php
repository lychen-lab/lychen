<?php

namespace App\Doctrine\Listener;

use App\Entity\Person;
use App\Entity\PersonApiKey;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: PersonApiKey::class)]
final readonly class PersonApiKeyLinkOwnerListener
{
    public function __construct(private Security $security,
        private LoggerInterface $logger)
    {
    }

    public function __invoke(PersonApiKey $personApiKey,
        LifecycleEventArgs $event): void
    {
        if (!$owner = $personApiKey->getPerson()) {
            $owner = $this->security->getUser();
        }

        if (!$owner instanceof Person) {
            return;
        }

        $personApiKey->setPerson($owner);

        $this->logger->info('User '.$owner->getUserIdentifier().' has created an API key',
            ['api_key_id' => $personApiKey->getId()]);
    }
}
