<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandTask;
use App\Factory\LandTaskFactory;

trait LandTaskTrait
{
    /**
     * @param array<string, mixed>|null $attributes
     */
    protected function createLandTask(Land $land, ?array $attributes = []): LandTask
    {
        return LandTaskFactory::new()->create([
            'land' => $land,
            ...$attributes,
        ]);
    }
}
