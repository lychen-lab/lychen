<?php

namespace App\Factory;

use App\Entity\SeedLibrary;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SeedLibrary>
 */
final class SeedLibraryFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return SeedLibrary::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => 'Grainothèque de '.self::faker()->city(),
            'city' => self::faker()->city(),
        ];
    }
}
