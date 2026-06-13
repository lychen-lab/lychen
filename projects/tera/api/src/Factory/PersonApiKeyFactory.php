<?php

namespace App\Factory;

use App\Entity\PersonApiKey;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PersonApiKey>
 */
final class PersonApiKeyFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return PersonApiKey::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(255),
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(PersonApiKey $personApiKey): void {})
            ;
    }
}
