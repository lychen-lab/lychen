<?php

declare(strict_types=1);

use App\Poc\Temporal\AreaProposalWorkflowInterface;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Sends a transition signal to a running AreaProposal workflow and prints its
 * current place.
 *
 * Usage:
 *   php bin/signal_workflow.php <areaProposalUuid> <submit|publish|reject|archive|status> [reason]
 */
$uuid = $argv[1] ?? null;
$action = $argv[2] ?? 'status';
$reason = $argv[3] ?? '';
$address = getenv('TEMPORAL_ADDRESS') ?: 'localhost:7233';

if (null === $uuid) {
    fwrite(\STDERR, "Missing AreaProposal UUID.\n");
    exit(1);
}

$client = WorkflowClient::create(ServiceClient::create($address));

/** @var AreaProposalWorkflowInterface $workflow */
$workflow = $client->newRunningWorkflowStub(
    AreaProposalWorkflowInterface::class,
    'area-proposal-' . $uuid,
);

switch ($action) {
    case 'submit':
        $workflow->submit();
        break;
    case 'publish':
        $workflow->publish();
        break;
    case 'reject':
        $workflow->reject($reason);
        break;
    case 'archive':
        $workflow->archive();
        break;
    case 'status':
        break;
    default:
        fwrite(\STDERR, sprintf("Unknown action \"%s\".\n", $action));
        exit(1);
}

// Queries are read-only and always reflect the latest workflow state.
echo sprintf("Place:   %s\n", $workflow->getPlace());
echo sprintf("History: %s\n", json_encode($workflow->getHistory(), \JSON_PRETTY_PRINT));
