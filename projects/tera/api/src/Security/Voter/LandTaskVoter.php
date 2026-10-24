<?php

namespace App\Security\Voter;

use App\Entity\LandTask;
use App\Security\Interface\PermissionHolder;

class LandTaskVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_task:delete';
    public const string PATCH = 'land_member:land_task:patch';
    public const string POST = 'land_member:land_task:post';
    public const string GET = 'land_member:land_task:get';
    public const string COLLECTION = 'land_member:land_task:collection';
    public const string MARK_AS_DONE = 'land_member:land_task:mark_as_done';
    public const string MARK_AS_IN_PROGRESS = 'land_member:land_task:mark_as_in_progress';

    public const array ALL = [
        self::DELETE,
        self::PATCH,
        self::GET,
        self::COLLECTION,
        self::POST,
        self::MARK_AS_DONE,
        self::MARK_AS_IN_PROGRESS,
    ];

    public const array ALL_PERSON = [];

    public const array ALL_LAND = [
        self::DELETE,
        self::POST,
        self::PATCH,
        self::GET,
        self::COLLECTION,
        self::MARK_AS_DONE,
        self::MARK_AS_IN_PROGRESS,
    ];

    public function getSupportedClass(): string
    {
        return LandTask::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }

    protected function voteOnCustomAttribute(string $attribute,
        mixed $subject,
        PermissionHolder $permissionHolder): bool
    {
        return match ($attribute) {
            self::MARK_AS_DONE => $this->canMarkAsDone($permissionHolder),
            self::MARK_AS_IN_PROGRESS => $this->canMarkAsInProgress($permissionHolder),
            default => parent::voteOnCustomAttribute($attribute, $subject, $permissionHolder),
        };
    }

    private function canMarkAsDone(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::MARK_AS_DONE);
    }

    private function canMarkAsInProgress(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::MARK_AS_IN_PROGRESS);
    }
}
