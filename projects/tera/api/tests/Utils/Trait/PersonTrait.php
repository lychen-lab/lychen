<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Person;
use App\Factory\PersonFactory;

trait PersonTrait
{
    /**
     * @param array<string, mixed>|callable $attributes
     */
    protected function createPerson(array|callable $attributes = []): Person
    {
        return PersonFactory::new()->create($attributes);
    }
}
