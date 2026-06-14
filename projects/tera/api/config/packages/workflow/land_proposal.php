<?php

declare(strict_types=1);

use App\Entity\LandProposal;
use App\Workflow\LandProposal\LandProposalWorkflow;
use App\Workflow\LandProposal\LandProposalWorkflowPlace;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            LandProposalWorkflow::NAME => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => LandProposalWorkflowPlace::DRAFT,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => LandProposalWorkflowPlace::PLACES,
                'supports' => [
                    LandProposal::class,
                ],
                'transitions' => [
                    LandProposalWorkflowTransition::PUBLISH => [
                        'from' => LandProposalWorkflowPlace::DRAFT,
                        'to' => LandProposalWorkflowPlace::PUBLISHED,
                    ],
                    LandProposalWorkflowTransition::ARCHIVE => [
                        'from' => LandProposalWorkflowPlace::PUBLISHED,
                        'to' => LandProposalWorkflowPlace::ARCHIVED,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
