<?php

namespace App\Temporal\Workflow;

use Temporal\Workflow\QueryMethod;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface ValidationAreaProposalWorkflowInterface
{
    #[WorkflowMethod(name: 'ValidationAreaProposalWorkflow')]
    public function run(string $proposalId): \Generator;

    #[SignalMethod]
    public function moderatorDecided(string $decision, ?string $reason = null): void;

    #[QueryMethod]
    public function getStatus(): string;
}
