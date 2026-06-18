<?php

declare(strict_types=1);

use App\Poc\Temporal\AreaProposalActivities;
use App\Poc\Temporal\AreaProposalWorkflow;
use Temporal\WorkerFactory;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * RoadRunner worker host. RoadRunner (configured in .rr.yml) boots this script
 * as one or more PHP processes and streams workflow/activity tasks to it over
 * gRPC. This is the equivalent of `messenger:consume` for Temporal.
 */
$factory = WorkerFactory::create();

$worker = $factory->newWorker('espace.area_proposal');

$worker->registerWorkflowTypes(AreaProposalWorkflow::class);
$worker->registerActivity(AreaProposalActivities::class);

$factory->run();
