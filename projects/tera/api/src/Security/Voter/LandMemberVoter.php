<?php

namespace App\Security\Voter;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Land;
use App\Entity\LandApiKey;
use App\Entity\LandMember;
use App\Security\Checker\LandApiKeyPermissionChecker;
use App\Security\Checker\LandMemberPermissionChecker;
use App\Security\Checker\PersonApiKeyPermissionChecker;
use App\Security\Checker\PersonPermissionChecker;
use App\Security\Interface\PermissionHolder;
use App\Security\Service\PermissionHolderRetriever;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class LandMemberVoter extends AbstractPermissionVoter
{
    public const string DELETE = 'land_member:land_member:delete';
    public const string PATCH = 'land_member:land_member:patch';
    public const string GET = 'land_member:land_member:get';
    public const string COLLECTION = 'land_member:land_member:collection';
    public const string ME = 'person:land_member:me';

    public const array ALL = [
        self::DELETE,
        self::PATCH,
        self::GET,
        self::COLLECTION,
        self::ME,
    ];

    public const array ALL_PERSON = [
        self::ME,
    ];

    public const array ALL_LAND = [
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
        private readonly ManagerRegistry $managerRegistry)
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
        $operationIsGetMe = $operation instanceof Get && $operation->getName() === 'land-member_me';

        $supportsSubject = $subject instanceof LandMember;
        $supportsAttribute = in_array($attribute, self::ALL);

        return ($supportsSubject || $operationIsPost || $operationIsCollection || $operationIsGetMe) && $supportsAttribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $permissionHolder = $this->getPermissionHolder($subject);

        return match ($attribute) {
            self::GET => $this->canGet($subject, $permissionHolder),
            self::PATCH => $this->canPatch($permissionHolder),
            self::DELETE => $this->canDelete($subject, $permissionHolder),
            self::COLLECTION => $this->canCollection($permissionHolder),
            self::ME => $this->canMe($permissionHolder),
            default => throw new LogicException($attribute . ' is not supported.')
        };
    }

    private function canGet(LandMember $landMember, PermissionHolder $permissionHolder): bool
    {
        if ($landMember === $permissionHolder) {
            return true;
        }

        return $this->can($permissionHolder, self::GET);
    }

    private function canPatch(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::PATCH);
    }

    private function canDelete(LandMember $landMember, PermissionHolder $permissionHolder): bool
    {
        if ($landMember->isOwner()) {
            return false;
        }

        if ($landMember === $permissionHolder) {
            return true;
        }

        return $this->can($permissionHolder, self::DELETE);
    }

    private function canCollection(PermissionHolder $permissionHolder): bool
    {
        if ($permissionHolder instanceof LandApiKey) {
            return $this->can($permissionHolder, self::COLLECTION);
        }

        $currentRequest = $this->requestStack->getCurrentRequest();
        $landUlid = $currentRequest->query->get('land');
        if (empty($landUlid)) {
            throw new HttpException(422, "Land query parameter shouldn't be empty");
        }

        $land = $this->managerRegistry->getRepository(Land::class)->findOneBy(['ulid' => $landUlid]);

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

    private function canMe(PermissionHolder $permissionHolder): bool
    {
        return $this->can($permissionHolder, self::ME);
    }
}
