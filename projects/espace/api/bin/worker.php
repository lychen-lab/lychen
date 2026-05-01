#!/usr/bin/env php
<?php

use App\Kernel;
use App\Temporal\Activity\ValidationAreaProposalActivities;
use App\Temporal\Workflow\ValidationAreaProposalWorkflow;
use Temporal\WorkerFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

$container = $kernel->getContainer();

$factory = WorkerFactory::create();

$worker = $factory->newWorker('area-proposal');

$worker->registerWorkflowTypes(ValidationAreaProposalWorkflow::class);

$worker->registerActivity(
    ValidationAreaProposalActivities::class,
    fn() => $container->get(ValidationAreaProposalActivities::class)
);

$factory->run();
