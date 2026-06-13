<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandArea;
use App\Factory\LandAreaFactory;

trait LandAreaTrait
{
    protected function createLandArea(Land $land): LandArea
    {
        return LandAreaFactory::new()->create([
            'land' => $land
        ]);
    }
}
