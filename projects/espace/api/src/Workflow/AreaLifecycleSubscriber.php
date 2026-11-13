<?php

namespace App\Workflow;

use App\Entity\AreaListingInterface;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

/**
 * The rules of a listing's lifecycle, bound to its workflow so that they hold whoever
 * applies a transition: the API today, the Temporal activities of the validation
 * workflows later.
 *
 * - A rejection needs a reason, set on the listing beforehand.
 * - Submitting clears that reason: it only makes sense while the listing is being fixed.
 * - Archiving dates the listing.
 */
final readonly class AreaLifecycleSubscriber implements EventSubscriberInterface
{
    public const string REJECTION_WITHOUT_REASON = 'd6a1c0c4-2f6b-4c56-9b1c-6d0f2f7a5a11';

    public function __construct(private ClockInterface $clock)
    {
    }

    public static function getSubscribedEvents(): array
    {
        $events = [];
        foreach ([AreaProposalWorkflow::NAME, AreaRequestWorkflow::NAME] as $workflow) {
            // Both workflows name these three transitions alike.
            $events[\sprintf('workflow.%s.guard.%s', $workflow, AreaProposalWorkflow::TRANSITION_REJECT)] = 'requireRejectionReason';
            $events[\sprintf('workflow.%s.completed.%s', $workflow, AreaProposalWorkflow::TRANSITION_SUBMIT)] = 'clearRejectionReason';
            $events[\sprintf('workflow.%s.completed.%s', $workflow, AreaProposalWorkflow::TRANSITION_ARCHIVE)] = 'dateArchiving';
        }

        return $events;
    }

    /**
     * @param GuardEvent<object> $event
     */
    public function requireRejectionReason(GuardEvent $event): void
    {
        $listing = $event->getSubject();
        if ($listing instanceof AreaListingInterface && '' === trim((string) $listing->getRejectionReason())) {
            $event->addTransitionBlocker(new TransitionBlocker('A rejection needs a reason.', self::REJECTION_WITHOUT_REASON));
        }
    }

    /**
     * @param CompletedEvent<object> $event
     */
    public function clearRejectionReason(CompletedEvent $event): void
    {
        $listing = $event->getSubject();
        if ($listing instanceof AreaListingInterface) {
            $listing->setRejectionReason(null);
        }
    }

    /**
     * @param CompletedEvent<object> $event
     */
    public function dateArchiving(CompletedEvent $event): void
    {
        $listing = $event->getSubject();
        if ($listing instanceof AreaListingInterface) {
            $listing->setArchivedAt($this->clock->now());
        }
    }
}
