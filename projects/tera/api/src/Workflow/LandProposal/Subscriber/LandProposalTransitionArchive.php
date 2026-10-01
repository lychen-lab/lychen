<?php

namespace App\Workflow\LandProposal\Subscriber;

use App\Entity\LandProposal;
use App\Workflow\LandProposal\LandProposalWorkflow;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;

readonly class LandProposalTransitionArchive implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName(LandProposalWorkflow::NAME,
                LandProposalWorkflowTransition::ARCHIVE) => [['onTransition', 0]],
        ];
    }

    public function onTransition(Event $event): void
    {
        /** @var LandProposal $landProposal */
        $landProposal = $event->getSubject();

        $landProposal->setArchivedAt(new \DateTimeImmutable());
    }
}
