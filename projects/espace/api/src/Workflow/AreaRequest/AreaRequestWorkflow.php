<?php

namespace App\Workflow\AreaRequest;

final class AreaRequestWorkflow
{
    public const string NAME = 'area_request_validation';
    public const string PLACE_DRAFT = 'draft';
    public const string PLACE_PENDING_VALIDATION = 'pending_validation';
    public const string PLACE_ACTIVE = 'active';
    public const string PLACE_REJECTED = 'rejected';
    public const string PLACE_ARCHIVED = 'archived';

    public const array PLACES = [
        self::PLACE_DRAFT,
        self::PLACE_PENDING_VALIDATION,
        self::PLACE_ACTIVE,
        self::PLACE_REJECTED,
        self::PLACE_ARCHIVED,
    ];

    public const string TRANSITION_SUBMIT = 'submit';
    public const string TRANSITION_APPROVE = 'approve';
    public const string TRANSITION_REJECT = 'reject';
    public const string TRANSITION_ARCHIVE = 'archive';

    public const array TRANSITIONS = [
        self::TRANSITION_SUBMIT,
        self::TRANSITION_APPROVE,
        self::TRANSITION_REJECT,
        self::TRANSITION_ARCHIVE,
    ];
}
