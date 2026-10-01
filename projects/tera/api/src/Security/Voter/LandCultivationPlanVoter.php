<?php

namespace App\Security\Voter;

use App\Entity\LandCultivationPlan;

class LandCultivationPlanVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_cultivation_plan:delete';
    public const string PATCH = 'land_member:land_cultivation_plan:patch';
    public const string POST = 'land_member:land_cultivation_plan:post';
    public const string GET = 'land_member:land_cultivation_plan:get';
    public const string COLLECTION = 'land_member:land_cultivation_plan:collection';

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
        return LandCultivationPlan::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }
}
