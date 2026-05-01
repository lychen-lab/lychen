<?php

namespace App\Temporal\Activity;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'ValidationAreaProposal.')]
interface ValidationAreaProposalActivitiesInterface
{
    #[ActivityMethod]
    public function runAutomatedChecks(string $proposalId): array;

    #[ActivityMethod]
    public function markApproved(string $proposalId): void;

    #[ActivityMethod]
    public function markRejected(string $proposalId, string $reason): void;

    #[ActivityMethod]
    public function queueForModeration(string $proposalId): void;

    #[ActivityMethod]
    public function notifyViaWebhook(string $event, string $proposalId): void;

    #[ActivityMethod]
    public function signalOpenMatchingWorkflows(string $proposalId): void;
}
