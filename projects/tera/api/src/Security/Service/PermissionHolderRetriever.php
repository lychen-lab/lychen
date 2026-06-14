<?php

namespace App\Security\Service;

use App\Entity\Land;
use App\Entity\LandApiKey;
use App\Entity\LandMember;
use App\Entity\Person;
use App\Repository\LandMemberRepository;
use App\Security\Constant\PersonPermission;
use App\Security\Interface\LandAwareInterface;
use App\Security\Interface\PermissionHolder;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

readonly class PermissionHolderRetriever
{
    public function __construct(private Security $security,
        private LandMemberRepository $landMemberRepository)
    {
    }

    public function fromContext(PermissionHolderRetrieverContext $context): ?PermissionHolder
    {
        $currentUser = $this->security->getUser();

        if ($context->subject instanceof LandAwareInterface) {
            if ($currentUser instanceof LandApiKey) {
                return $currentUser;
            }
            try {
                $land = $context->subject->getLand();
                if ($currentUser instanceof Person && null !== $land) {
                    return $this->getLandMember($land, $currentUser);
                }
            } catch (Exception $exception) {

            }
        }

        if ($currentUser instanceof Person) {
            $currentUser->setPermissions(PersonPermission::ALL);
        }

        if (!$currentUser instanceof PermissionHolder) {
            throw new Exception('No permission holder available for this context');
        }

        return $currentUser;
    }

    public function getLandMember(Land $land,
        PermissionHolder $permissionHolder): LandMember
    {
        if (!$permissionHolder instanceof Person) {
            throw new Exception('User must be an instance of Person');
        }
        $landMember = $this->landMemberRepository->findOneBy(['land' => $land, 'person' => $permissionHolder]);
        if (null === $landMember) {
            throw new Exception('Unable to find LandMember related to the authenticated user.');
        }
        return $landMember;
    }
}
