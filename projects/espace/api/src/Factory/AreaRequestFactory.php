<?php

namespace App\Factory;

use App\Entity\AreaRequest;
use Override;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AreaRequest>
 */
final class AreaRequestFactory extends PersistentObjectFactory
{
    #[Override]
    public static function class(): string
    {
        return AreaRequest::class;
    }

    #[Override]
    protected function defaults(): array|callable
    {
        return [

            'description' => self::faker()->text(),
            'title' => self::faker()->text(255),
        ];
    }

    #[Override]
    protected function initialize(): static
    {
        return $this;
    }
}
