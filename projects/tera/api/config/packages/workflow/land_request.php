<?php

declare(strict_types=1);

use App\Entity\LandRequest;
use App\Workflow\LandRequest\LandRequestWorkflow;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use App\Workflow\LandRequest\LandRequestWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            LandRequestWorkflow::NAME => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => LandRequestWorkflowPlace::DRAFT,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => LandRequestWorkflowPlace::PLACES,
                'supports' => [
                    LandRequest::class,
                ],
                'transitions' => [
                    LandRequestWorkflowTransition::PUBLISH => [
                        'from' => LandRequestWorkflowPlace::DRAFT,
                        'to' => LandRequestWorkflowPlace::PUBLISHED,
                    ],
                    LandRequestWorkflowTransition::ARCHIVE => [
                        'from' => LandRequestWorkflowPlace::PUBLISHED,
                        'to' => LandRequestWorkflowPlace::ARCHIVED,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
