<?php

namespace App\Workflow\LandMemberInvitation\Subscriber;

use App\Entity\LandMemberInvitation;
use App\Service\Land\LandMemberInvitationManager;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflow;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowTransition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;

readonly class LandMemberInvitationTransitionAccept implements EventSubscriberInterface
{
    public function __construct(private LandMemberInvitationManager $landMemberInvitationManager, private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName(LandMemberInvitationWorkflow::NAME, LandMemberInvitationWorkflowTransition::ACCEPT) => [['onTransition', 0], ['createLandMember', 5]],
        ];
    }

    public function onTransition(Event $event): void
    {
        /** @var LandMemberInvitation $landMemberInvitation */
        $landMemberInvitation = $event->getSubject();

        $landMemberInvitation->setAcceptedAt(new \DateTimeImmutable());
    }

    public function createLandMember(Event $event): void
    {
        /** @var LandMemberInvitation $landMemberInvitation */
        $landMemberInvitation = $event->getSubject();

        $this->landMemberInvitationManager->linkToAuthenticatedPerson($landMemberInvitation);
        $landMember = $this->landMemberInvitationManager->createLandMember($landMemberInvitation);

        $this->entityManager->persist($landMember);
    }
}
