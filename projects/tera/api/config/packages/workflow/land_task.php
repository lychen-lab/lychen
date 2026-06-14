<?php

declare(strict_types=1);

use App\Entity\LandTask;
use App\Workflow\LandTask\LandTaskWorkflow;
use App\Workflow\LandTask\LandTaskWorkflowPlace;
use App\Workflow\LandTask\LandTaskWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            LandTaskWorkflow::NAME => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => LandTaskWorkflowPlace::TO_BE_DONE,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => LandTaskWorkflowPlace::PLACES,
                'supports' => [
                    LandTask::class,
                ],
                'transitions' => [
                    LandTaskWorkflowTransition::MARK_AS_IN_PROGRESS => [
                        'from' => LandTaskWorkflowPlace::TO_BE_DONE,
                        'to' => LandTaskWorkflowPlace::IN_PROGRESS,
                    ],
                    LandTaskWorkflowTransition::MARK_AS_DONE => [
                        'from' => [
                            LandTaskWorkflowPlace::TO_BE_DONE,
                            LandTaskWorkflowPlace::IN_PROGRESS,
                        ],
                        'to' => LandTaskWorkflowPlace::DONE,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
