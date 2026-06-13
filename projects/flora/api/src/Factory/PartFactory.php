<?php

namespace App\Factory;

use App\Entity\Part;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Part>
 */
final class PartFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return Part::class;
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
            // ->afterInstantiate(function(Part $part): void {})
        ;
    }
}
