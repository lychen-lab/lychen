<?php

namespace App\Api\Transform;

use App\Entity\SeedLibrary;

/**
 * @extends AbstractUuidToEntity<SeedLibrary>
 */
final class UuidToSeedLibrary extends AbstractUuidToEntity
{
    protected function entityClass(): string
    {
        return SeedLibrary::class;
    }
}
