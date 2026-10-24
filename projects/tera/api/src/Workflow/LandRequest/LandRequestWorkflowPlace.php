<?php

namespace App\Workflow\LandRequest;

final class LandRequestWorkflowPlace
{
    public const string DRAFT = 'draft';
    public const string PUBLISHED = 'published';
    public const string ARCHIVED = 'archived';

    public const array PLACES = [
        self::DRAFT,
        self::PUBLISHED,
        self::ARCHIVED,
    ];
}
