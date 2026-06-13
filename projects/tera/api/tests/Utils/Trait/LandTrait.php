<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\Person;
use App\Factory\LandFactory;

trait LandTrait
{
    public static function landDataProvider(): array
    {
        return [
            [10, -20],
            [200, 20],
            [8700, 1430],
        ];
    }

    protected function createLand(Person $person): Land
    {
        return LandFactory::new()->create([
            'owner' => $person
        ]);
    }
}
