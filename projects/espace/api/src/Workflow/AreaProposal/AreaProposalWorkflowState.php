<?php

namespace App\Workflow\AreaProposal;

final class AreaProposalWorkflowState
{
    public const string DRAFT = 'draft';
    public const string VERIFICATION = 'verification';
    public const string PUBLISHED = 'published';
    public const string ARCHIVED = 'archived';

    public const array ALL = [
        self::DRAFT,
        self::VERIFICATION,
        self::PUBLISHED,
        self::ARCHIVED,
    ];
}
