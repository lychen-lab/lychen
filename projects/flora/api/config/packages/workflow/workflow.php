<?php

declare(strict_types=1);

use App\Entity\SeedLot;
use App\Workflow\SeedLot\SeedLotWorkflow;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            SeedLotWorkflow::NAME => [
                'initial_marking' => SeedLotWorkflow::PLACE_AVAILABLE,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => SeedLotWorkflow::PLACES,
                'supports' => [
                    SeedLot::class,
                ],
                'transitions' => [
                    SeedLotWorkflow::TRANSITION_RESERVE => [
                        'from' => SeedLotWorkflow::PLACE_AVAILABLE,
                        'to' => SeedLotWorkflow::PLACE_RESERVED,
                    ],
                    SeedLotWorkflow::TRANSITION_RELEASE => [
                        'from' => SeedLotWorkflow::PLACE_RESERVED,
                        'to' => SeedLotWorkflow::PLACE_AVAILABLE,
                    ],
                    SeedLotWorkflow::TRANSITION_GIVE => [
                        'from' => SeedLotWorkflow::PLACE_RESERVED,
                        'to' => SeedLotWorkflow::PLACE_GIVEN,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
