<?php

namespace App\Factory;

use App\Entity\Family;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Family>
 */
final class FamilyFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return Family::class;
    }


    protected function defaults(): array|callable
    {
        return [
            'code' => self::faker()->text(100),
        ];
    }


    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Family $family): void {})
        ;
    }
}
