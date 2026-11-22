<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandRole;
use App\Factory\LandRoleFactory;

trait LandRoleTrait
{
    /**
     * @param list<string>|null $permissions
     */
    protected function createLandRole(Land $land, ?array $permissions = null): LandRole
    {
        return LandRoleFactory::new()->create([
            'land' => $land,
            'permissions' => $permissions,
        ]);
    }
}
