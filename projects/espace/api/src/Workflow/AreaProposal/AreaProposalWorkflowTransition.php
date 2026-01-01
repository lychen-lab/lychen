<?php

namespace App\Workflow\AreaProposal;

final class AreaProposalWorkflowTransition
{
    public const string SUBMIT = 'submit';
    public const string PUBLISH = 'publish';
    public const string REJECT = 'reject';
    public const string ARCHIVE = 'archive';

    public const array ALL = [
        self::SUBMIT,
        self::PUBLISH,
        self::REJECT,
        self::ARCHIVE,
    ];
}
