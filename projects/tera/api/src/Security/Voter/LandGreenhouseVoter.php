<?php

namespace App\Security\Voter;

use App\Entity\LandGreenhouse;

class LandGreenhouseVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_greenhouse:delete';
    public const string PATCH = 'land_member:land_greenhouse:patch';
    public const string POST = 'land_member:land_greenhouse:post';
    public const string GET = 'land_member:land_greenhouse:get';
    public const string COLLECTION = 'land_member:land_greenhouse:collection';

    public const array ALL = [
        self::DELETE,
        self::PATCH,
        self::GET,
        self::COLLECTION,
        self::POST,
    ];

    public const array ALL_PERSON = [];

    public const array ALL_LAND = [
        self::DELETE,
        self::POST,
        self::PATCH,
        self::GET,
        self::COLLECTION,
    ];

    public function getSupportedClass(): string
    {
        return LandGreenhouse::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }
}
