<?php

namespace App\Security\Voter;

use App\Entity\LandHarvestEntry;

class LandHarvestEntryVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_harvest_entry:delete';
    public const string PATCH = 'land_member:land_harvest_entry:patch';
    public const string POST = 'land_member:land_harvest_entry:post';
    public const string GET = 'land_member:land_harvest_entry:get';
    public const string COLLECTION = 'land_member:land_harvest_entry:collection';

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
        return LandHarvestEntry::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }
}
