<?php

namespace App\Workflow;

use App\Entity\AreaListingInterface;
use App\Entity\AreaProposal;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\Registry;

/**
 * Moves a listing (area proposal or request) through its lifecycle: the entry point of
 * anything that is not an API operation, first of all the activities of
 * ValidationAreaProposalWorkflow and ValidationAreaRequestWorkflow.
 *
 * It only changes the listing: persisting it is the caller's business (an activity
 * checks the place before calling, then flushes). The rules themselves are in
 * AreaLifecycleSubscriber, and apply to the API's transition operations just the same.
 *
 * Every method throws NotEnabledTransitionException when the listing's place does not
 * allow the transition.
 */
final readonly class AreaLifecycle
{
    public function __construct(private Registry $workflows)
    {
    }

    public function isEditable(AreaListingInterface $listing): bool
    {
        // Both workflows start from the same place.
        return AreaProposalWorkflow::PLACE_DRAFT === $listing->getPlace();
    }

    /**
     * @throws NotEnabledTransitionException
     */
    public function submit(AreaListingInterface $listing): void
    {
        $this->apply($listing, AreaProposalWorkflow::TRANSITION_SUBMIT);
    }

    /**
     * Moderation's approval: publishes a proposal, activates a request.
     *
     * @throws NotEnabledTransitionException
     */
    public function accept(AreaListingInterface $listing): void
    {
        $this->apply($listing, $listing instanceof AreaProposal
            ? AreaProposalWorkflow::TRANSITION_PUBLISH
            : AreaRequestWorkflow::TRANSITION_APPROVE);
    }

    /**
     * Sends the listing back to its owner as a draft, with the reason to show them.
     *
     * @throws NotEnabledTransitionException
     */
    public function reject(AreaListingInterface $listing, string $reason): void
    {
        $previousReason = $listing->getRejectionReason();
        $listing->setRejectionReason($reason);

        try {
            $this->apply($listing, AreaProposalWorkflow::TRANSITION_REJECT);
        } catch (NotEnabledTransitionException $exception) {
            $listing->setRejectionReason($previousReason);

            throw $exception;
        }
    }

    /**
     * @throws NotEnabledTransitionException
     */
    public function archive(AreaListingInterface $listing): void
    {
        $this->apply($listing, AreaProposalWorkflow::TRANSITION_ARCHIVE);
    }

    private function apply(AreaListingInterface $listing, string $transition): void
    {
        $this->workflows->get($listing)->apply($listing, $transition);
    }
}
