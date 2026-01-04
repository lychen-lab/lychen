<?php

declare(strict_types=1);

use App\Entity\AreaProposal;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            AreaProposalWorkflow::NAME => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => AreaProposalWorkflow::PLACE_DRAFT,
                'marking_store' => [
                    'property' => 'place',
                    'type' => 'method',
                ],
                'places' => AreaProposalWorkflow::PLACES,
                'supports' => [
                    AreaProposal::class,
                ],
                'transitions' => [
                    AreaProposalWorkflow::TRANSITION_ARCHIVE => [
                        'from' => [
                            AreaProposalWorkflow::PLACE_VERIFICATION,
                            AreaProposalWorkflow::PLACE_PUBLISHED,
                        ],
                        'to' => AreaProposalWorkflow::PLACE_ARCHIVED,
                    ],
                    AreaProposalWorkflow::TRANSITION_PUBLISH => [
                        'from' => AreaProposalWorkflow::PLACE_VERIFICATION,
                        'to' => AreaProposalWorkflow::PLACE_PUBLISHED,
                    ],
                    AreaProposalWorkflow::TRANSITION_REJECT => [
                        'from' => AreaProposalWorkflow::PLACE_VERIFICATION,
                        'to' => AreaProposalWorkflow::PLACE_DRAFT,
                    ],
                    AreaProposalWorkflow::TRANSITION_SUBMIT => [
                        'from' => AreaProposalWorkflow::PLACE_DRAFT,
                        'to' => AreaProposalWorkflow::PLACE_VERIFICATION,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
