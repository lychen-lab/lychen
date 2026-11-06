<?php

namespace App\Workflow\SeedLot;

/**
 * The life of a seed lot in its library: available, reserved by a member, then given.
 */
final class SeedLotWorkflow
{
    public const string NAME = 'seed_lot';

    public const string PLACE_AVAILABLE = 'available';
    public const string PLACE_RESERVED = 'reserved';
    public const string PLACE_GIVEN = 'given';

    public const array PLACES = [
        self::PLACE_AVAILABLE,
        self::PLACE_RESERVED,
        self::PLACE_GIVEN,
    ];

    public const string TRANSITION_RESERVE = 'reserve';
    public const string TRANSITION_RELEASE = 'release';
    public const string TRANSITION_GIVE = 'give';

    public const array TRANSITIONS = [
        self::TRANSITION_RESERVE,
        self::TRANSITION_RELEASE,
        self::TRANSITION_GIVE,
    ];
}
