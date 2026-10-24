<?php

namespace App\Security\Voter;

use App\Entity\LandAreaSetting;

class LandAreaSettingVoter extends AbstractLandAwareVoterInterface
{
    public const string PATCH = 'land_member:land_area_setting:patch';
    public const string GET = 'land_member:land_area_setting:get';

    public const array ALL = [
        self::PATCH,
        self::GET,
    ];

    public const array ALL_PERSON = [];

    public const array ALL_LAND = [
        self::PATCH,
        self::GET,
    ];

    public function getSupportedClass(): string
    {
        return LandAreaSetting::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }
}
