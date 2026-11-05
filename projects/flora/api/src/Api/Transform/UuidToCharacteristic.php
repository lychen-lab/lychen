<?php

namespace App\Api\Transform;

use App\Entity\Characteristic;

/**
 * @extends AbstractUuidToEntity<Characteristic>
 */
final class UuidToCharacteristic extends AbstractUuidToEntity
{
    protected function entityClass(): string
    {
        return Characteristic::class;
    }
}
