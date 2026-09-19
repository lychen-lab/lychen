<?php

namespace App\Factory;

use App\Entity\AreaRequest;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentObjectFactory<AreaRequest>
 */
final class AreaRequestFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return AreaRequest::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(),
            'title' => self::faker()->text(255),
            'city' => self::faker()->city(),
            'minimalSurfaceRequested' => self::faker()->numberBetween(5, 1000),
            // Lazy so that tests overriding these relations don't need existing rows.
            'requester' => lazy(static fn () => PersonFactory::randomOrCreate()),
            'activities' => lazy(static fn () => AreaActivityFactory::randomRangeOrCreate(1, 5)),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
