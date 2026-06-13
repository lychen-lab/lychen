<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Land;
use App\Entity\LandApiKey;
use App\Security\Interface\PermissionHolder;
use Exception;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class LandVoter extends AbstractPermissionVoter
{
    public const string POST = 'person:land:post';
    public const string DELETE = 'land_member:land:delete';
    public const string PATCH = 'land_member:land:patch';
    public const string GET = 'person-land_member:land:get';
    public const string COLLECTION = 'person:land:collection';

    public const array ALL = [
        self::POST,
        self::DELETE,
        self::PATCH,
        self::GET,
        self::COLLECTION,
    ];

    public const array ALL_PERSON = [
        self::POST,
        self::GET,
        self::COLLECTION,
    ];

    public const array ALL_LAND = [
        self::DELETE,
        self::PATCH,
        self::GET,
    ];

    protected function supports(string $attribute,
                                mixed  $subject): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $operation = $currentRequest->attributes->get('_api_operation');
        $operationIsPost = $operation instanceof Post;
        $operationIsCollection = $operation instanceof GetCollection;

        $supportsSubject = $subject instanceof Land;
        $supportsAttribute = in_array($attribute, self::ALL);

        return ($supportsSubject || $operationIsPost || $operationIsCollection) && $supportsAttribute;
    }

    protected function voteOnAttribute(string         $attribute,
                                       mixed          $subject,
                                       TokenInterface $token, ?Vote $vote = null): bool
    {
        $permissionHolder = $this->getPermissionHolder($subject);

        return match ($attribute) {
            self::GET => $this->canGet($subject, $permissionHolder),
            self::POST => $this->canPost($permissionHolder),
            self::PATCH => $this->canPatch($subject, $permissionHolder),
            self::DELETE => $this->canDelete($subject, $permissionHolder),
            self::COLLECTION => $this->canCollection($permissionHolder),
            default => throw new LogicException($attribute . ' is not supported.')
        };
    }

    private function canGet(Land $land, PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($land, $permissionHolder, self::GET);
    }

    private function canPerformAction(Land             $land,
                                      PermissionHolder $permissionHolder,
                                      string           $action): bool
    {
        if ($permissionHolder instanceof LandApiKey) {
            $hasPermission = $this->can($permissionHolder, $action);

            if (!$hasPermission) {
                return false;
            }

            return $land->getUlid() === $permissionHolder->getLand()->getUlid();
        }

        try {
            $landMember = $this->permissionHolderRetriever->getLandMember($land, $permissionHolder);
        } catch (Exception) {
            return false;
        }

        return $this->can($landMember, $action);
    }

    private function canPost(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::POST);
    }

    private function canPatch(Land             $land,
                              PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($land, $permissionHolder, self::PATCH);
    }

    private function canDelete(Land             $land,
                               PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($land, $permissionHolder, self::DELETE);
    }

    private function canCollection(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::COLLECTION);
    }
}
