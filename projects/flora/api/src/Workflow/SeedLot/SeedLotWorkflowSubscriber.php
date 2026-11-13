<?php

namespace App\Workflow\SeedLot;

use App\Entity\SeedLot;
use App\Event\Outgoing\SeedLotGiven;
use Lychen\EventBus\Outbox\Outbox;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Workflow\Attribute\AsCompletedListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\Event;

/**
 * What the transitions of SeedLotWorkflow do besides changing the lot's state.
 *
 * Every listener runs inside WorkflowTransitionProcessor's transaction, between the lock on
 * the lot and the commit: what they change, the event recorded in the outbox included, is
 * committed with the new state or not at all.
 */
final readonly class SeedLotWorkflowSubscriber
{
    public function __construct(
        private Security $security,
        private Outbox $outbox,
        private ClockInterface $clock,
    ) {
    }

    /**
     * Remembers who reserved the lot: the member it is given to.
     *
     * @param Event<object> $event
     */
    #[AsTransitionListener(workflow: SeedLotWorkflow::NAME, transition: SeedLotWorkflow::TRANSITION_RESERVE)]
    public function onReserve(Event $event): void
    {
        $this->seedLot($event)->setReservedBy($this->security->getUser()?->getUserIdentifier());
    }

    /**
     * @param Event<object> $event
     */
    #[AsTransitionListener(workflow: SeedLotWorkflow::NAME, transition: SeedLotWorkflow::TRANSITION_RELEASE)]
    public function onRelease(Event $event): void
    {
        $this->seedLot($event)->setReservedBy(null);
    }

    /**
     * Tells the other services the lot was given. Recorded in the outbox, not published: the
     * relay publishes it once this transaction is committed.
     *
     * @param Event<object> $event
     */
    #[AsCompletedListener(workflow: SeedLotWorkflow::NAME, transition: SeedLotWorkflow::TRANSITION_GIVE)]
    public function onGiven(Event $event): void
    {
        $lot = $this->seedLot($event);

        $this->outbox->record(new SeedLotGiven(
            seedLot: $lot->getUuid(),
            seedLibrary: $lot->getSeedLibrary()->getUuid(),
            variety: $lot->getVariety(),
            harvestYear: $lot->getHarvestYear(),
            quantityInGrams: $lot->getQuantityInGrams(),
            recipient: $lot->getReservedBy(),
            givenAt: $this->clock->now(),
        ));
    }

    /**
     * @param Event<object> $event
     */
    private function seedLot(Event $event): SeedLot
    {
        $subject = $event->getSubject();

        return $subject instanceof SeedLot
            ? $subject
            : throw new \LogicException(\sprintf('The "%s" workflow only supports %s, got %s.', SeedLotWorkflow::NAME, SeedLot::class, get_debug_type($subject)));
    }
}
