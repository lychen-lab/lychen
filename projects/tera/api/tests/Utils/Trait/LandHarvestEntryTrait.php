<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandHarvestEntry;
use App\Factory\LandHarvestEntryFactory;

trait LandHarvestEntryTrait
{
    /**
     * @param array<string, mixed>|null $attributes
     */
    protected function createLandHarvestEntry(Land $land, ?array $attributes = []): LandHarvestEntry
    {
        return LandHarvestEntryFactory::new()->create([
            'land' => $land,
            ...$attributes,
        ]);
    }
}
