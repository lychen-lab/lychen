<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Person;
use App\Entity\PersonApiKey;
use App\Security\Interface\PermissionHolder;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class PersonApiKeyVoter extends AbstractPermissionVoter
{

    public const string DELETE = 'person:person_api_key:delete';
    public const string POST = 'person:person_api_key:post';
    public const string GET = 'person:person_api_key:get';
    public const string COLLECTION = 'person:person_api_key:collection';

    public const array ALL = [
        self::DELETE,
        self::GET,
        self::COLLECTION,
        self::POST,
    ];

    public const array ALL_PERSON = [
        self::DELETE,
        self::POST,
        self::GET,
        self::COLLECTION,
    ];

    public const array ALL_LAND = [

    ];

    protected function supports(string $attribute,
                                mixed  $subject): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $operation = $currentRequest->attributes->get('_api_operation');
        $operationIsPost = $operation instanceof Post;
        $operationIsCollection = $operation instanceof GetCollection;

        $supportsSubject = $subject instanceof PersonApiKey;
        $supportsAttribute = in_array($attribute, self::ALL);

        return ($supportsSubject || $operationIsPost || $operationIsCollection) && $supportsAttribute;
    }

    protected function voteOnAttribute(string         $attribute,
                                       mixed          $subject,
                                       TokenInterface $token, ?Vote $vote = null): bool
    {
        $permissionHolder = $this->getPermissionHolder($subject);

        return match ($attribute) {
            self::GET => $this->canGet($permissionHolder),
            self::POST => $this->canPost($permissionHolder),
            self::DELETE => $this->canDelete($subject, $permissionHolder),
            self::COLLECTION => $this->canCollection($permissionHolder),
            default => throw new LogicException($attribute . ' is not supported.')
        };
    }

    private function canGet($permissionHolder): bool
    {
        return $this->can($permissionHolder, self::GET);
    }

    private function canPost($permissionHolder): bool
    {
        if (!$permissionHolder instanceof Person) {
            return false;
        }
        return $this->can($permissionHolder, self::POST);
    }

    private function canDelete(PersonApiKey     $personApiKey,
                               PermissionHolder $permissionHolder): bool
    {
        $hasPermission = $this->can($permissionHolder, self::DELETE);

        if (!$hasPermission) {
            return false;
        }

        $userIsOwner = $personApiKey->getPerson() === $permissionHolder;

        return $userIsOwner;

    }

    private function canCollection(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::COLLECTION);
    }
}

