<?php

namespace App\Factory;

use App\Entity\Cultivation;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class CultivationFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Cultivation::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'exposure' => ExposureFactory::new(),
            'maturity' => MaturityFactory::new(),
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Cultivation $cultivation): void {})
            ;
    }
}
