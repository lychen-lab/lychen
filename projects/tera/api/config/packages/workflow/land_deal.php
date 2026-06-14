<?php

declare(strict_types=1);

use App\Entity\LandDeal;
use App\Workflow\LandDeal\LandDealWorkflowPlace;
use App\Workflow\LandDeal\LandDealWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            'land_deal' => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => LandDealWorkflowPlace::OPENED,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => LandDealWorkflowPlace::PLACES,
                'supports' => [
                    LandDeal::class,
                ],
                'transitions' => [
                    LandDealWorkflowTransition::ACCEPT => [
                        'from' => LandDealWorkflowPlace::OPENED,
                        'to' => LandDealWorkflowPlace::ACCEPTED,
                    ],
                    LandDealWorkflowTransition::ARCHIVE => [
                        'from' => [
                            LandDealWorkflowPlace::OPENED,
                            LandDealWorkflowPlace::ACCEPTED,
                            LandDealWorkflowPlace::REFUSED,
                        ],
                        'to' => LandDealWorkflowPlace::ARCHIVED,
                    ],
                    LandDealWorkflowTransition::REFUSE => [
                        'from' => LandDealWorkflowPlace::OPENED,
                        'to' => LandDealWorkflowPlace::REFUSED,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
