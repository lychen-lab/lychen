<?php

namespace App\Tests\Utils\Trait;


use App\Entity\LandRequest;
use App\Entity\Person;
use App\Factory\LandRequestFactory;

trait LandRequestTrait
{
    protected function createLandRequest(Person $person, ?array $attributes = null): LandRequest
    {
        return LandRequestFactory::new()->create(array_merge([
            'person' => $person
        ], $attributes ?? []));
    }
}
