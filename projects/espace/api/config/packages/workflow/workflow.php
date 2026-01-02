<?php

declare(strict_types=1);

use App\Entity\AreaProposal;
use App\Workflow\AreaProposal\AreaProposalWorkflowState;
use App\Workflow\AreaProposal\AreaProposalWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            'area_proposal' => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => AreaProposalWorkflowState::DRAFT,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => AreaProposalWorkflowState::ALL,
                'supports' => [
                    AreaProposal::class,
                ],
                'transitions' => [
                    AreaProposalWorkflowTransition::ARCHIVE => [
                        'from' => [
                            AreaProposalWorkflowState::VERIFICATION,
                            AreaProposalWorkflowState::PUBLISHED,
                        ],
                        'to' => AreaProposalWorkflowState::ARCHIVED,
                    ],
                    AreaProposalWorkflowTransition::PUBLISH => [
                        'from' => AreaProposalWorkflowState::VERIFICATION,
                        'to' => AreaProposalWorkflowState::PUBLISHED,
                    ],
                    AreaProposalWorkflowTransition::REJECT => [
                        'from' => AreaProposalWorkflowState::VERIFICATION,
                        'to' => AreaProposalWorkflowState::DRAFT,
                    ],
                    AreaProposalWorkflowTransition::SUBMIT => [
                        'from' => AreaProposalWorkflowState::DRAFT,
                        'to' => AreaProposalWorkflowState::VERIFICATION,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
