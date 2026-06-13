<?php

namespace App\Factory;

use App\Entity\Exposure;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Exposure>
 */
final class ExposureFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return Exposure::class;
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
            // ->afterInstantiate(function(Exposure $exposure): void {})
        ;
    }
}
