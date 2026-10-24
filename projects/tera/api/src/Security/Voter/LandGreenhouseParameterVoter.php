<?php

namespace App\Security\Voter;

use App\Entity\LandGreenhouseParameter;

class LandGreenhouseParameterVoter extends AbstractLandAwareVoterInterface
{
    public const string PATCH = 'land_member:land_greenhouse_parameter:patch';
    public const string GET = 'land_member:land_greenhouse_parameter:get';

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
        return LandGreenhouseParameter::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }
}
