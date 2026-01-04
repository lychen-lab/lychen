<?php

namespace App\Workflow\AreaProposal;

final class AreaProposalWorkflow
{
    public const string NAME = 'area_proposal_publishing';
    public const string PLACE_DRAFT = 'draft';
    public const string PLACE_VERIFICATION = 'verification';
    public const string PLACE_PUBLISHED = 'published';
    public const string PLACE_ARCHIVED = 'archived';

    public const array PLACES = [
        self::PLACE_DRAFT,
        self::PLACE_VERIFICATION,
        self::PLACE_PUBLISHED,
        self::PLACE_ARCHIVED,
    ];

    public const string TRANSITION_SUBMIT = 'submit';
    public const string TRANSITION_PUBLISH = 'publish';
    public const string TRANSITION_REJECT = 'reject';
    public const string TRANSITION_ARCHIVE = 'archive';

    public const array TRANSITIONS = [
        self::TRANSITION_SUBMIT,
        self::TRANSITION_PUBLISH,
        self::TRANSITION_REJECT,
        self::TRANSITION_ARCHIVE,
    ];
}
