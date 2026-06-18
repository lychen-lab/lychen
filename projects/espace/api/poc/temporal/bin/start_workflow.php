<?php

declare(strict_types=1);

use App\Poc\Temporal\AreaProposalWorkflowInterface;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowOptions;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Starts one AreaProposal workflow execution.
 *
 * Usage:
 *   php bin/start_workflow.php [areaProposalUuid] [temporalAddress]
 *
 * The WorkflowId is derived from the UUID so the same proposal can never have
 * two concurrent executions (Temporal rejects duplicate WorkflowIds) — a
 * uniqueness guarantee the Symfony component does not give you.
 */
$uuid = $argv[1] ?? bin2hex(random_bytes(8));
$address = $argv[2] ?? (getenv('TEMPORAL_ADDRESS') ?: 'localhost:7233');

$client = WorkflowClient::create(ServiceClient::create($address));

$workflow = $client->newWorkflowStub(
    AreaProposalWorkflowInterface::class,
    WorkflowOptions::new()
        ->withTaskQueue('espace.area_proposal')
        ->withWorkflowId('area-proposal-' . $uuid),
);

$run = $client->start($workflow, $uuid);

echo sprintf("Started workflow for AreaProposal %s\n", $uuid);
echo sprintf("  WorkflowId: %s\n", $run->getExecution()->getID());
echo sprintf("  RunId:      %s\n", $run->getExecution()->getRunID());
echo "\nDrive it with:\n";
echo sprintf("  php bin/signal_workflow.php %s submit\n", $uuid);
echo sprintf("  php bin/signal_workflow.php %s publish\n", $uuid);
echo sprintf("  php bin/signal_workflow.php %s archive\n", $uuid);
