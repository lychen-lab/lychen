<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandApiKey;
use App\Factory\LandApiKeyFactory;

trait LandApiKeyTrait
{
    protected function createLandApiKey(Land $land, array|callable $attributes = [], array $excludedPermissions = []): LandApiKey
    {
        if ($attributes['permissions'] && $excludedPermissions) {
            $attributes['permissions'] = array_diff($attributes['permissions'], $excludedPermissions);
        }
        return LandApiKeyFactory::new()->create(array_merge(['land' => $land], $attributes));
    }
}
