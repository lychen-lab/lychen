<?php

namespace App\Factory;

use App\Entity\Person;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

use function Zenstruck\Foundry\faker;

/**
 * @extends PersistentObjectFactory<Person>
 */
final class PersonFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Person::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'authId' => Uuid::v7(),
            'email' => faker()->email(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
