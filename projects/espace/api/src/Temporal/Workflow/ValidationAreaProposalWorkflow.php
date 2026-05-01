<?php

namespace App\Temporal\Workflow;

use App\Temporal\Activity\ValidationAreaProposalActivitiesInterface;
use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;

#[WorkflowInterface]
class ValidationAreaProposalWorkflow implements ValidationAreaProposalWorkflowInterface
{
    private string $status = 'pending_validation';

    private bool $moderatorDecisionReceived = false;

    private string $moderatorDecision = '';

    private ?string $moderatorReason = null;

    private ValidationAreaProposalActivitiesInterface $activities;

    public function __construct()
    {
        $this->activities = Workflow::newActivityStub(
            ValidationAreaProposalActivitiesInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(30))
        );
    }

    public function run(string $proposalId): \Generator
    {
        $checkResult = yield $this->activities->runAutomatedChecks($proposalId);

        if (!($checkResult['passed'] ?? false)) {
            $reason = $checkResult['reason'] ?? 'Automated checks failed';
            yield $this->activities->markRejected($proposalId, $reason);
            yield $this->activities->notifyViaWebhook('area_proposal.rejected', $proposalId);
            $this->status = 'rejected';

            return;
        }

        yield $this->activities->queueForModeration($proposalId);
        $this->status = 'awaiting_moderation';

        // Wait up to 48h for a moderator signal; auto-approve on timeout
        yield Workflow::awaitWithTimeout(
            CarbonInterval::hours(48),
            fn() => $this->moderatorDecisionReceived
        );

        if ($this->moderatorDecisionReceived && $this->moderatorDecision === 'rejected') {
            $reason = $this->moderatorReason ?? 'Rejected by moderator';
            yield $this->activities->markRejected($proposalId, $reason);
            yield $this->activities->notifyViaWebhook('area_proposal.rejected', $proposalId);
            $this->status = 'rejected';

            return;
        }

        yield $this->activities->markApproved($proposalId);
        yield $this->activities->notifyViaWebhook('area_proposal.approved', $proposalId);
        yield $this->activities->signalOpenMatchingWorkflows($proposalId);
        $this->status = 'active';
    }

    public function moderatorDecided(string $decision, ?string $reason = null): void
    {
        $this->moderatorDecision = $decision;
        $this->moderatorReason = $reason;
        $this->moderatorDecisionReceived = true;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
