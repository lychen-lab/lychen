<?php

namespace App\Factory;

use App\Entity\Characteristic;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Characteristic>
 */
final class CharacteristicFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Characteristic::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'code' => self::faker()->regexify('[a-z]{16}'),
            'label' => self::faker()->words(2, true),
        ];
    }
}
