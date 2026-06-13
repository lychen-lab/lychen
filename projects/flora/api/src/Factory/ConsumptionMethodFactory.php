<?php

namespace App\Factory;

use App\Entity\ConsumptionMethod;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ConsumptionMethod>
 */
final class ConsumptionMethodFactory extends PersistentObjectFactory
{

    public function __construct()
    {
    }

    public static function class(): string
    {
        return ConsumptionMethod::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'code' => self::faker()->text(100),
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(ConsumptionMethod $exposure): void {})
            ;
    }
}
