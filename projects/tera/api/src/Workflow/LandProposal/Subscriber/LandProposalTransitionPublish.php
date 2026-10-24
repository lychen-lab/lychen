<?php

namespace App\Workflow\LandProposal\Subscriber;

use App\Entity\LandProposal;
use App\Workflow\LandProposal\LandProposalWorkflow;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;

readonly class LandProposalTransitionPublish implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName(LandProposalWorkflow::NAME,
                LandProposalWorkflowTransition::PUBLISH) => [['onTransition', 0]],
        ];
    }

    public function onTransition(Event $event): void
    {
        /** @var LandProposal $landProposal */
        $landProposal = $event->getSubject();

        $landProposal->setPublishedAt(new \DateTimeImmutable());
        $expirationDate = (new \DateTime())->modify('+1 month');

        $landProposal->setExpirationDate(\DateTimeImmutable::createFromMutable($expirationDate));
    }
}
