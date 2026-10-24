<?php

namespace App\Service\Land;

use App\Entity\LandMember;
use App\Entity\LandMemberInvitation;
use App\Entity\Person;
use PHPUnit\Framework\Exception;
use Symfony\Bundle\SecurityBundle\Security;

readonly class LandMemberInvitationManager
{
    public function __construct(private Security $security)
    {
    }

    public function linkToAuthenticatedPerson(LandMemberInvitation &$landMemberInvitation): LandMemberInvitation
    {
        $person = $this->security->getUser();

        if (null === $person) {
            throw new Exception('User should be authenticated to link a LandMemberInvitation to a person');
        }

        if (!$person instanceof Person) {
            throw new Exception('Authenticated user should be a Person');
        }

        if ($person->getEmail() !== $landMemberInvitation->getEmail()) {
            throw new Exception('Authenticated user should have the same email as the LandMemberInvitation email property');
        }

        return $landMemberInvitation->setPerson($person);
    }

    public function createLandMember(LandMemberInvitation $landMemberInvitation): LandMember
    {
        if (null === $landMemberInvitation->getPerson()) {
            throw new Exception('Person should be set to LandMemberInvitation before creating a land member');
        }

        return (new LandMember())
            ->setLand($landMemberInvitation->getLand())
            ->setLandRoles($landMemberInvitation->getLandRoles())
            ->setPerson($landMemberInvitation->getPerson());
    }
}
