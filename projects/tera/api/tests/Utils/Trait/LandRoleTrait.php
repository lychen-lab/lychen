<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandRole;
use App\Factory\LandRoleFactory;

trait LandRoleTrait
{
    protected function createLandRole(Land $land, ?array $permissions = null): LandRole
    {
        return LandRoleFactory::new()->create([
            'land' => $land,
            'permissions' => $permissions,
        ]);
    }
}
