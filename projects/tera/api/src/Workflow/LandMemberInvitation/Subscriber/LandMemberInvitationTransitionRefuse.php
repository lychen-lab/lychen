<?php

namespace App\Workflow\LandMemberInvitation\Subscriber;

use App\Entity\LandMemberInvitation;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflow;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowTransition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;

readonly class LandMemberInvitationTransitionRefuse implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName(LandMemberInvitationWorkflow::NAME, LandMemberInvitationWorkflowTransition::REFUSE) => [['onTransition', 0], ['sendEmailToLandOwner', 5]],
        ];
    }

    public function onTransition(Event $event): void
    {
        /** @var LandMemberInvitation $landMemberInvitation */
        $landMemberInvitation = $event->getSubject();

        $landMemberInvitation->setRefusedAt(new \DateTimeImmutable());
    }

    public function sendEmailToLandOwner(Event $event): void
    {
        /** @var LandMemberInvitation $landMemberInvitation */
        $landMemberInvitation = $event->getSubject();

        // TODO Send email to LandOwner
    }
}
