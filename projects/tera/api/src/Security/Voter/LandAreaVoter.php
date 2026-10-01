<?php

namespace App\Security\Voter;

use App\Entity\LandArea;

class LandAreaVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_area:delete';
    public const string PATCH = 'land_member:land_area:patch';
    public const string POST = 'land_member:land_area:post';
    public const string GET = 'land_member:land_area:get';
    public const string COLLECTION = 'land_member:land_area:collection';

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
        return LandArea::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }
}
