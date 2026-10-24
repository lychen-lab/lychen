<?php

namespace App\Workflow\LandRequest\Subscriber;

use App\Entity\LandRequest;
use App\Workflow\LandRequest\LandRequestWorkflow;
use App\Workflow\LandRequest\LandRequestWorkflowTransition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;

readonly class LandRequestTransitionPublish implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName(LandRequestWorkflow::NAME, LandRequestWorkflowTransition::PUBLISH) => [['onTransition', 0]],
        ];
    }

    public function onTransition(Event $event): void
    {
        /** @var LandRequest $landRequest */
        $landRequest = $event->getSubject();

        $landRequest->setPublishedAt(new \DateTimeImmutable());
        $expirationDate = (new \DateTime())->modify('+1 month');

        $landRequest->setExpirationDate(\DateTimeImmutable::createFromMutable($expirationDate));
    }
}
