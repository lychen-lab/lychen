<?php

namespace App\Factory;

use App\Entity\AreaActivity;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AreaActivity>
 */
final class AreaActivityFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return AreaActivity::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'code' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
