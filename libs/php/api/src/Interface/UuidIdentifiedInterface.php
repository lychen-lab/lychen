<?php

namespace Lychen\Api\Interface;

use Symfony\Component\Uid\Uuid;

/**
 * The contract of an entity the API identifies by uuid.
 *
 * lychen/api does not require an entity to implement this interface itself: an identical
 * contract declared by another package (lychen/model's UuidIdentifiedInterface) fulfills it
 * too, as does any entity with the same getUuid(). See UuidContract.
 */
interface UuidIdentifiedInterface
{
    public function getUuid(): Uuid;

    public function setUuid(Uuid $uuid): self;
}
