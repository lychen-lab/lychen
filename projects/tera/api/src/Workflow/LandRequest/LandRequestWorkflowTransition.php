<?php

namespace App\Workflow\LandRequest;

final class LandRequestWorkflowTransition
{
    public const string PUBLISH = 'publish';
    public const string ARCHIVE = 'archive';

    public const array TRANSITIONS = [
        self::PUBLISH,
        self::ARCHIVE,
    ];
}
