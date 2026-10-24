<?php

namespace App\Security\Voter;

use App\Entity\LandApiKey;
use App\Entity\LandMember;
use App\Security\Interface\PermissionHolder;

class LandApiKeyVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_api_key:delete';
    public const string POST = 'land_member:land_api_key:post';
    public const string GET = 'land_member:land_api_key:get';
    public const string COLLECTION = 'land_member:land_api_key:collection';

    public const array ALL = [
        self::DELETE,
        self::GET,
        self::COLLECTION,
        self::POST,
    ];

    public const array ALL_PERSON = [];

    public const array ALL_LAND = [
        self::DELETE,
        self::POST,
        self::GET,
        self::COLLECTION,
    ];

    public function getSupportedClass(): string
    {
        return LandApiKey::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }

    protected function canPost(PermissionHolder $permissionHolder,
        string $permission): bool
    {
        if (!$permissionHolder instanceof LandMember) {
            return false;
        }

        return parent::canPost($permissionHolder, $permission);
    }
}
