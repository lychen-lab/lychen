<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandMemberInvitation;
use App\Entity\LandRole;
use App\Factory\LandMemberInvitationFactory;

use function Zenstruck\Foundry\faker;

trait LandMemberInvitationTrait
{
    /**
     * @param list<LandRole>|null $roles
     */
    protected function createLandMemberInvitation(Land $land, ?string $email, ?array $roles = null): LandMemberInvitation
    {
        return LandMemberInvitationFactory::new()->create([
            'land' => $land,
            'email' => $email ?? faker()->email(),
            'landRoles' => $roles ?? [],
        ]);
    }
}
