<?php

namespace App\Factory;

use App\Entity\Person;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use function Zenstruck\Foundry\faker;

/**
 * @extends PersistentProxyObjectFactory<Person>
 */
final class PersonFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Person::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'authId' => Uuid::v4(),
            'email' => faker()->email()
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
