<?php

declare(strict_types=1);

use App\Entity\AreaProposal;
use App\Entity\AreaRequest;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            AreaRequestWorkflow::NAME => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => AreaRequestWorkflow::PLACE_DRAFT,
                'marking_store' => [
                    'property' => 'place',
                    'type' => 'method',
                ],
                'places' => AreaRequestWorkflow::PLACES,
                'supports' => [
                    AreaRequest::class,
                ],
                'transitions' => [
                    AreaRequestWorkflow::TRANSITION_ARCHIVE => [
                        'from' => [
                            AreaRequestWorkflow::PLACE_ACTIVE,
                            AreaRequestWorkflow::PLACE_REJECTED,
                        ],
                        'to' => AreaRequestWorkflow::PLACE_ARCHIVED,
                    ],
                    AreaRequestWorkflow::TRANSITION_APPROVE => [
                        'from' => AreaRequestWorkflow::PLACE_PENDING_VALIDATION,
                        'to' => AreaRequestWorkflow::PLACE_ACTIVE,
                    ],
                    AreaRequestWorkflow::TRANSITION_REJECT => [
                        'from' => AreaRequestWorkflow::PLACE_PENDING_VALIDATION,
                        'to' => AreaRequestWorkflow::PLACE_REJECTED,
                    ],
                    AreaRequestWorkflow::TRANSITION_SUBMIT => [
                        'from' => AreaRequestWorkflow::PLACE_DRAFT,
                        'to' => AreaRequestWorkflow::PLACE_PENDING_VALIDATION,
                    ],
                ],
                'type' => 'state_machine',
            ],
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
