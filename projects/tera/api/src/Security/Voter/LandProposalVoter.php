<?php

namespace App\Security\Voter;

use App\Entity\LandApiKey;
use App\Entity\LandMember;
use App\Entity\LandProposal;
use App\Security\Interface\LandAwareInterface;
use App\Security\Interface\PermissionHolder;
use App\Workflow\LandProposal\LandProposalWorkflowPlace;

class LandProposalVoter extends AbstractLandAwareVoterInterface
{
    public const string POST = 'land_member:land_proposal:post';
    public const string PATCH = 'land_member:land_proposal:patch';
    public const string GET = 'person-land_member:land_proposal:get';
    public const string COLLECTION = 'land_member:land_proposal:collection';
    public const string COLLECTION_PUBLIC = 'person:land_proposal:collection_public';
    public const string DELETE = 'land_member:land_proposal:delete';
    public const string PUBLISH = 'land_member:land_proposal:publish';
    public const string ARCHIVE = 'land_member:land_proposal:archive';

    public const array ALL = [
        self::POST,
        self::PATCH,
        self::DELETE,
        self::GET,
        self::COLLECTION,
        self::PUBLISH,
        self::ARCHIVE,
        self::COLLECTION_PUBLIC,
    ];

    public const array ALL_PERSON = [
        self::GET,
        self::COLLECTION_PUBLIC,
    ];

    public const array ALL_LAND = [
        self::POST,
        self::GET,
        self::PATCH,
        self::DELETE,
        self::PUBLISH,
        self::ARCHIVE,
        self::COLLECTION,
    ];

    public function getSupportedClass(): string
    {
        return LandProposal::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }

    /**
     * @param LandProposal $subject
     */
    protected function voteOnCustomAttribute(string $attribute,
        mixed $subject,
        PermissionHolder $permissionHolder): bool
    {
        return match ($attribute) {
            self::COLLECTION_PUBLIC => $this->canCollectionPublic($permissionHolder),
            self::PUBLISH => $this->canPublish($permissionHolder),
            self::ARCHIVE => $this->canArchive($permissionHolder),
            default => parent::voteOnCustomAttribute($attribute, $subject, $permissionHolder),
        };
    }

    private function canCollectionPublic(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::COLLECTION_PUBLIC);
    }

    private function canPublish(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::PUBLISH);
    }

    private function canArchive(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::ARCHIVE);
    }

    /**
     * @param LandProposal $subject
     */
    protected function canGet(LandAwareInterface $subject,
        PermissionHolder $permissionHolder,
        string $permission): bool
    {
        $hasPermission = $this->can($permissionHolder, self::GET);

        if (!$hasPermission) {
            return false;
        }

        if (!$permissionHolder instanceof LandMember && !$permissionHolder instanceof LandApiKey) {
            return LandProposalWorkflowPlace::PUBLISHED === $subject->getState();
        }

        return parent::canGet($subject, $permissionHolder, $permission);
    }
}
