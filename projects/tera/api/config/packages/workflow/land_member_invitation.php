<?php

declare(strict_types=1);

use App\Entity\LandMemberInvitation;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflow;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowPlace;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowTransition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'workflows' => [
            LandMemberInvitationWorkflow::NAME => [
                'audit_trail' => [
                    'enabled' => true,
                ],
                'initial_marking' => LandMemberInvitationWorkflowPlace::PENDING,
                'marking_store' => [
                    'property' => 'state',
                    'type' => 'method',
                ],
                'places' => LandMemberInvitationWorkflowPlace::PLACES,
                'supports' => [
                    LandMemberInvitation::class,
                ],
                'transitions' => [
                    LandMemberInvitationWorkflowTransition::ACCEPT => [
                        'from' => LandMemberInvitationWorkflowPlace::PENDING,
                        'to' => LandMemberInvitationWorkflowPlace::ACCEPTED,
                    ],
                    LandMemberInvitationWorkflowTransition::REFUSE => [
                        'from' => LandMemberInvitationWorkflowPlace::PENDING,
                        'to' => LandMemberInvitationWorkflowPlace::REFUSED,
                    ],
                ],
                'type' => 'state_machine',
            ],
        ],
    ]);
};
