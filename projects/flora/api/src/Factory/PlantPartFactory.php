<?php

namespace App\Factory;

use App\Entity\PlantPart;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PlantPart>
 */
final class PlantPartFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return PlantPart::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'plant' => PlantFactory::new(),
            'part' => PartFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(PlantPart $exposure): void {})
            ;
    }
}
