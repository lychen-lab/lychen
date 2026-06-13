<?php

namespace App\Factory;

use App\Entity\SoilType;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SoilType>
 */
final class SoilTypeFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return SoilType::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'code' => self::faker()->text(100),
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(SoilType $exposure): void {})
            ;
    }
}
