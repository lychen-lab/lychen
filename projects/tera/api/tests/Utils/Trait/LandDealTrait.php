<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandDeal;
use App\Entity\Person;
use App\Factory\LandDealFactory;

trait LandDealTrait
{
    protected function createLandDeal(Land $land, Person $person, ?array $attributes = null): LandDeal
    {
        return LandDealFactory::new()->create(array_merge([
            'land' => $land,
            'person' => $person,
        ], $attributes ?? []));
    }
}
