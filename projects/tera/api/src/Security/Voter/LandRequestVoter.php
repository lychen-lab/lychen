<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\LandRequest;
use App\Entity\PersonApiKey;
use App\Security\Interface\PermissionHolder;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class LandRequestVoter extends AbstractPermissionVoter
{
    public const string POST = 'person:land_request:post';
    public const string PATCH = 'person:land_request:patch';
    public const string GET = 'person:land_request:get';
    public const string COLLECTION = 'person:land_request:collection';
    public const string COLLECTION_PUBLIC = 'person:land_request:collection_public';
    public const string DELETE = 'person:land_request:delete';
    public const string PUBLISH = 'person:land_request:publish';
    public const string ARCHIVE = 'person:land_request:archive';

    public const array ALL = [
        self::POST,
        self::PATCH,
        self::DELETE,
        self::PUBLISH,
        self::ARCHIVE,
        self::GET,
        self::COLLECTION,
        self::COLLECTION_PUBLIC,
    ];

    public const array ALL_PERSON = [
        self::POST,
        self::PATCH,
        self::DELETE,
        self::PUBLISH,
        self::ARCHIVE,
        self::GET,
        self::COLLECTION,
        self::COLLECTION_PUBLIC,
    ];

    protected function supports(string $attribute,
                                mixed  $subject): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $operation = $currentRequest->attributes->get('_api_operation');
        $operationIsPost = $operation instanceof Post;
        $operationIsCollection = $operation instanceof GetCollection;

        $supportsSubject = $subject instanceof LandRequest;
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
            self::PUBLISH => $this->canPublish($subject, $permissionHolder),
            self::ARCHIVE => $this->canArchive($subject, $permissionHolder),
            self::COLLECTION => $this->canCollection($permissionHolder),
            self::COLLECTION_PUBLIC => $this->canCollectionPublic($permissionHolder),
            default => throw new LogicException($attribute . ' is not supported.')
        };
    }

    private function canGet(LandRequest      $landRequest,
                            PermissionHolder $permissionHolder): bool
    {
        $hasPermission = $this->can($permissionHolder, self::GET);

        if (!$hasPermission) {
            return false;
        }

        if ($permissionHolder instanceof PersonApiKey) {
            $isOnBehalfOfUser = $landRequest->getPerson() === $permissionHolder->getPerson();
            return $isOnBehalfOfUser;
        }

        $userIsOwner = $landRequest->getPerson() === $permissionHolder;
        $userIsNotOwnerButStateIsPublished = $landRequest->getPerson() !== $permissionHolder && $landRequest->getState() === LandRequestWorkflowPlace::PUBLISHED;

        return $userIsOwner || $userIsNotOwnerButStateIsPublished;
    }

    private function canPost($permissionHolder): bool
    {
        return $this->can($permissionHolder, self::POST);
    }

    private function canPatch(LandRequest      $landRequest,
                              PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($landRequest, $permissionHolder, self::PATCH, LandRequestWorkflowPlace::DRAFT);
    }

    private function canPerformAction(LandRequest      $landRequest,
                                      PermissionHolder $permissionHolder,
                                      string           $action,
                                      ?string          $requiredState = null): bool
    {
        $hasPermission = $this->can($permissionHolder, $action);

        if (!$hasPermission) {
            return false;
        }

        if ($permissionHolder instanceof PersonApiKey) {
            $isOnBehalfOfUser = $landRequest->getPerson() === $permissionHolder->getPerson();
            return $isOnBehalfOfUser;
        }

        $userIsOwner = $landRequest->getPerson() === $permissionHolder;

        if ($requiredState !== null) {
            return $userIsOwner && $landRequest->getState() === $requiredState;
        }

        return $userIsOwner;
    }

    private function canDelete(LandRequest      $landRequest,
                               PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($landRequest, $permissionHolder, self::DELETE, LandRequestWorkflowPlace::DRAFT);
    }

    private function canPublish(LandRequest      $landRequest,
                                PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($landRequest, $permissionHolder, self::PUBLISH, LandRequestWorkflowPlace::DRAFT);
    }

    private function canArchive(LandRequest      $landRequest,
                                PermissionHolder $permissionHolder): bool
    {
        return $this->canPerformAction($landRequest, $permissionHolder, self::ARCHIVE,
            LandRequestWorkflowPlace::PUBLISHED);
    }

    private function canCollection(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::COLLECTION);
    }

    private function canCollectionPublic(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::COLLECTION_PUBLIC);
    }

}
