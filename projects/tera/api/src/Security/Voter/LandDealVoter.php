<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Post;
use App\Entity\Land;
use App\Entity\LandApiKey;
use App\Entity\LandDeal;
use App\Security\Checker\LandApiKeyPermissionChecker;
use App\Security\Checker\LandMemberPermissionChecker;
use App\Security\Checker\PersonApiKeyPermissionChecker;
use App\Security\Checker\PersonPermissionChecker;
use App\Security\Interface\PermissionHolder;
use App\Security\Service\PermissionHolderRetriever;
use Exception;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class LandDealVoter extends AbstractPermissionVoter
{
    public const string DELETE = 'land_member:land_deal:delete';
    public const string PATCH = 'land_member:land_deal:patch';
    public const string POST = 'person-land_member:land_deal:post';
    public const string GET = 'land_member:land_deal:get';
    public const string COLLECTION = 'land_member:land_deal:collection';

    public const array ALL = [
        self::DELETE,
        self::PATCH,
        self::GET,
        self::COLLECTION,
        self::POST,
    ];

    public const array ALL_PERSON = [
        self::POST,
    ];

    public const array ALL_LAND = [
        self::POST,
        self::DELETE,
        self::PATCH,
        self::GET,
        self::COLLECTION,
    ];

    public function __construct(PermissionHolderRetriever $permissionHolderRetriever,
        PersonApiKeyPermissionChecker $personApiKeyPermissionChecker,
        LandApiKeyPermissionChecker $landApiKeyPermissionChecker,
        PersonPermissionChecker $personPermissionChecker,
        LandMemberPermissionChecker $landMemberPermissionChecker,
        RequestStack $requestStack,
        private readonly IriConverterInterface $iriConverter)
    {
        parent::__construct($permissionHolderRetriever,
            $personApiKeyPermissionChecker,
            $landApiKeyPermissionChecker,
            $personPermissionChecker,
            $landMemberPermissionChecker,
            $requestStack);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $operation = $currentRequest->attributes->get('_api_operation');
        $operationIsPost = $operation instanceof Post;
        $operationIsCollection = $operation instanceof GetCollection;

        $supportsSubject = $subject instanceof LandDeal;
        $supportsAttribute = in_array($attribute, self::ALL);

        return ($supportsSubject || $operationIsPost || $operationIsCollection) && $supportsAttribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $permissionHolder = $this->getPermissionHolder($subject);

        return match ($attribute) {
            self::GET => $this->canGet($subject, $permissionHolder),
            self::PATCH => $this->canPatch($permissionHolder),
            self::DELETE => $this->canDelete($subject, $permissionHolder),
            self::COLLECTION => $this->canCollection($permissionHolder),
            self::POST => $this->canPost($permissionHolder),
            default => throw new LogicException($attribute . ' is not supported.')
        };
    }

    private function canGet(LandDeal $landDeal, PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::GET);
    }

    private function canPatch(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::PATCH);
    }

    private function canDelete(LandDeal $landDeal, PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::DELETE);
    }

    private function canCollection(PermissionHolder $permissionHolder): bool
    {
        if ($permissionHolder instanceof LandApiKey) {
            return $this->can($permissionHolder, self::COLLECTION);
        }

        $currentRequest = $this->requestStack->getCurrentRequest();
        $landIri = $currentRequest->query->get('land');
        if ($landIri === null) {
            throw new HttpException(400, '`land` query parameter is required.');
        }

        $land = $this->iriConverter->getResourceFromIri($landIri);

        if (!$land instanceof Land) {
            throw new LogicException('Land not found.');
        }

        try {
            $landMember = $this->permissionHolderRetriever->getLandMember($land, $permissionHolder);
        } catch (Exception $exception) {
            throw new HttpException(403, $exception->getMessage());
        }

        return $this->can($landMember, self::COLLECTION);
    }

    private function canPost(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::POST);
    }
}
