<?php

namespace App\Factory;

use App\Entity\LandApiKey;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<LandApiKey>
 */
final class LandApiKeyFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return LandApiKey::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(255),
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(LandApiKey $personApiKey): void {})
            ;
    }
}
