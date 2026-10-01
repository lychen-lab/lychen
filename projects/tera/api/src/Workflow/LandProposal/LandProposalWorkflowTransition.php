<?php

namespace App\Workflow\LandProposal;

final class LandProposalWorkflowTransition
{
    public const string PUBLISH = 'publish';
    public const string ARCHIVE = 'archive';

    public const array TRANSITIONS = [
        self::PUBLISH,
        self::ARCHIVE,
    ];
}
