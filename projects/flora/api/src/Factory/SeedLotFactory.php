<?php

namespace App\Factory;

use App\Entity\SeedLot;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SeedLot>
 */
final class SeedLotFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return SeedLot::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'variety' => self::faker()->words(2, true),
            'harvestYear' => self::faker()->numberBetween(2015, 2026),
            'quantityInGrams' => self::faker()->numberBetween(1, 500),
            'seedLibrary' => SeedLibraryFactory::new(),
        ];
    }
}
