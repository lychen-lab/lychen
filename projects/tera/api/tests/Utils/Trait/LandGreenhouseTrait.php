<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandGreenhouse;
use App\Factory\LandGreenhouseFactory;

trait LandGreenhouseTrait
{
    protected function createLandGreenhouse(Land $land): LandGreenhouse
    {
        return LandGreenhouseFactory::new()->create([
            'land' => $land
        ]);
    }
}
