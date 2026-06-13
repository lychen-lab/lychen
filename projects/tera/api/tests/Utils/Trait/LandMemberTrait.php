<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandMember;
use App\Entity\Person;
use App\Factory\LandMemberFactory;

trait LandMemberTrait
{
    protected function createLandMember(Land $land, Person $person, ?array $roles = null): LandMember
    {
        return LandMemberFactory::new()->create([
            'land' => $land,
            'person' => $person,
            'owner' => false,
            'landRoles' => $roles ?? [],
        ]);
    }
}
