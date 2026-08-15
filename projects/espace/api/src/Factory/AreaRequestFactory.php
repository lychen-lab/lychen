<?php

namespace App\Factory;

use App\Entity\AreaRequest;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

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
            'requester' => PersonFactory::random(),
            'activities' => AreaActivityFactory::randomSet(self::faker()->numberBetween(1, 5)),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
