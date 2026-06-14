<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class LandMemberInvitationCheckEmailUnicityDto
{
    public function __construct(
        #[Groups(['land_member_invitation:check-email-unicity'])] public bool $isUnique
    )
    {
    }
}
