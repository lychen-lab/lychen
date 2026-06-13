<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandCultivationPlan;
use App\Factory\LandCultivationPlanFactory;

trait LandCultivationPlanTrait
{
    protected function createLandCultivationPlan(Land $land): LandCultivationPlan
    {
        return LandCultivationPlanFactory::new()->create([
            'land' => $land
        ]);
    }
}
