<?php

declare(strict_types=1);

namespace App\Poc\Temporal;

use Temporal\Workflow\QueryMethod;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

/**
 * Temporal counterpart of the Symfony state machine
 * `App\Workflow\AreaProposal\AreaProposalWorkflow` (state machine
 * `area_proposal_publishing`).
 *
 * Places:      draft -> verification -> published -> archived
 * Transitions: submit, publish, reject, archive
 *
 * Where the Symfony component is a passive set of rules applied synchronously
 * inside an HTTP request, the Temporal workflow is a long-lived, durable
 * orchestration: it is started once for a given AreaProposal and then driven
 * by signals (submit / publish / reject / archive) until it reaches a final
 * place. The current place is exposed through a query so the API can read it
 * without touching the database.
 */
#[WorkflowInterface]
interface AreaProposalWorkflowInterface
{
    /**
     * Runs the full publishing lifecycle of an AreaProposal until it is
     * archived (the only terminal place).
     *
     * @param string $areaProposalUuid the UUID of the AreaProposal entity
     *
     * @return string the final place reached by the proposal
     */
    #[WorkflowMethod(name: 'espace.AreaProposalWorkflow')]
    public function run(string $areaProposalUuid): \Generator;

    /** draft -> verification */
    #[SignalMethod]
    public function submit(): void;

    /** verification -> published */
    #[SignalMethod]
    public function publish(): void;

    /** verification -> draft */
    #[SignalMethod]
    public function reject(string $reason = ''): void;

    /** verification|published -> archived */
    #[SignalMethod]
    public function archive(): void;

    /** Current place of the proposal (draft|verification|published|archived). */
    #[QueryMethod]
    public function getPlace(): string;

    /**
     * Ordered audit trail of the transitions applied so far.
     *
     * @return list<array{transition: string, from: string, to: string}>
     */
    #[QueryMethod]
    public function getHistory(): array;
}
