<?php

declare(strict_types=1);

use App\Entity\LandArea;
use App\Workflow\LandArea\LandAreaWorkflowPlace;
use App\Workflow\LandArea\LandAreaWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            'land_area' => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => LandAreaWorkflowPlace::ACTIVE,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => LandAreaWorkflowPlace::PLACES,
                'supports' => [
                    LandArea::class,
                ],
                'transitions' => [
                    LandAreaWorkflowTransition::ARCHIVE => [
                        'from' => LandAreaWorkflowPlace::ACTIVE,
                        'to' => LandAreaWorkflowPlace::ARCHIVED,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
