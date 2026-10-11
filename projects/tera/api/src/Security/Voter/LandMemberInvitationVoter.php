<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\LandMemberInvitation;
use App\Entity\Person;
use App\Entity\PersonApiKey;
use App\Security\Interface\PermissionHolder;

class LandMemberInvitationVoter extends AbstractLandAwareVoterInterface
{
    public const string DELETE = 'land_member:land_member_invitation:delete';
    public const string PATCH = 'land_member:land_member_invitation:patch';
    public const string POST = 'land_member:land_member_invitation:post';
    public const string GET = 'land_member:land_member_invitation:get';
    public const string COLLECTION = 'land_member:land_member_invitation:collection';
    public const string CHECK_EMAIL_UNICITY = 'land_member:land_member_invitation:check_email_unicity';
    public const string COLLECTION_BY_EMAIL = 'person:land_member_invitation:collection_by_email';
    public const string ACCEPT = 'person:land_member_invitation:accept';
    public const string REFUSE = 'person:land_member_invitation:refuse';

    public const array ALL = [
        self::DELETE,
        self::PATCH,
        self::POST,
        self::GET,
        self::COLLECTION,
        self::CHECK_EMAIL_UNICITY,
        self::COLLECTION_BY_EMAIL,
        self::ACCEPT,
        self::REFUSE,
    ];

    public const array ALL_PERSON = [
        self::ACCEPT,
        self::REFUSE,
        self::COLLECTION_BY_EMAIL,
    ];

    public const array ALL_LAND = [
        self::DELETE,
        self::PATCH,
        self::POST,
        self::GET,
        self::COLLECTION,
        self::CHECK_EMAIL_UNICITY,
    ];

    protected function supports(string $attribute,
        mixed $subject,
    ): bool {
        $this->currentRequest = $this->requestStack->getCurrentRequest();
        $operation = $this->currentRequest->attributes->get('_api_operation');
        $operationIsPost = $operation instanceof Post;
        $operationIsCollection = $operation instanceof GetCollection;

        $operationIsCheckUnicity = $operation instanceof Get && 'land-member-invitation_check-email-unicity' === $operation->getName();

        $supportsSubject = $subject instanceof ($this->getSupportedClass());
        $supportsAttribute = in_array($attribute, $this->getAvailablePermissions());

        return ($supportsSubject || $operationIsPost || $operationIsCollection || $operationIsCheckUnicity) && $supportsAttribute;
    }

    public function getSupportedClass(): string
    {
        return LandMemberInvitation::class;
    }

    public function getAvailablePermissions(): array
    {
        return self::ALL;
    }

    /**
     * @param LandMemberInvitation $subject
     */
    protected function voteOnCustomAttribute(string $attribute,
        mixed $subject,
        PermissionHolder $permissionHolder): bool
    {
        return match ($attribute) {
            self::ACCEPT => $this->canAccept($subject),
            self::REFUSE => $this->canRefuse($subject),
            self::COLLECTION_BY_EMAIL => $this->canCollectionByEmail($permissionHolder),
            self::CHECK_EMAIL_UNICITY => $this->canCheckEmailUnicity($permissionHolder),
            default => parent::voteOnCustomAttribute($attribute, $subject, $permissionHolder),
        };
    }

    private function canAccept(LandMemberInvitation $subject): bool
    {
        $permissionHolder = $this->getPermissionHolder(null);

        return $this->can($permissionHolder, self::ACCEPT) && $this->isEmailMatchingWithCurrentUser($subject,
            $permissionHolder);
    }

    private function isEmailMatchingWithCurrentUser(LandMemberInvitation $subject,
        PermissionHolder $permissionHolder): bool
    {
        $email = null;
        if ($permissionHolder instanceof Person) {
            $email = $permissionHolder->getEmail();
        }

        if ($permissionHolder instanceof PersonApiKey) {
            $email = $permissionHolder->getPerson()->getEmail();
        }

        if ($subject->getEmail() === $email) {
            return true;
        }

        return false;
    }

    private function canRefuse(LandMemberInvitation $subject): bool
    {
        $permissionHolder = $this->getPermissionHolder(null);

        return $this->can($permissionHolder, self::REFUSE) && $this->isEmailMatchingWithCurrentUser($subject,
            $permissionHolder);
    }

    private function canCollectionByEmail(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::COLLECTION_BY_EMAIL);
    }

    private function canCheckEmailUnicity(PermissionHolder $permissionHolder): bool
    {
        return $this->canCollection($permissionHolder, self::CHECK_EMAIL_UNICITY);
    }
}
