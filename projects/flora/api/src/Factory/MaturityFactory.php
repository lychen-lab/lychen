<?php

namespace App\Factory;

use App\Entity\Maturity;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Maturity>
 */
final class MaturityFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return Maturity::class;
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
            // ->afterInstantiate(function(Maturity $maturity): void {})
        ;
    }
}
