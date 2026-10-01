<?php

namespace App\Doctrine\Listener;

use App\Entity\LandMemberInvitation;
use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onNewPerson', entity: Person::class)]
#[AsEntityListener(event: Events::postPersist, method: 'onNewMemberInvitation', entity: LandMemberInvitation::class)]
final readonly class LandMemberInvitationAutoLink
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onNewPerson(Person $person, LifecycleEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $landMemberInvitationRepository = $entityManager->getRepository(LandMemberInvitation::class);

        try {
            $landMemberInvitations = $landMemberInvitationRepository->findBy(['email' => $person->getEmail()]);

            foreach ($landMemberInvitations as $landMemberInvitation) {
                $landMemberInvitation->setPerson($person);
                $entityManager->persist($landMemberInvitation);
            }

            $entityManager->flush();
        } catch (\Throwable $e) {
            $this->logger->error('An error occurred while linking LandMemberInvitations to Person', [
                'exception' => $e,
                'personId' => $person->getId(),
            ]);
        }
    }

    public function onNewMemberInvitation(LandMemberInvitation $landMemberInvitation, LifecycleEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $personRepository = $entityManager->getRepository(Person::class);

        try {
            $person = $personRepository->findOneBy(['email' => $landMemberInvitation->getEmail()]);

            if ($person) {
                $landMemberInvitation->setPerson($person);
                $entityManager->persist($landMemberInvitation);
                $entityManager->flush();
            }
        } catch (\Throwable $e) {
            $this->logger->error('An error occurred while linking Person to LandMemberInvitation', [
                'exception' => $e,
                'landMemberInvitationId' => $landMemberInvitation->getId(),
            ]);
        }
    }
}
