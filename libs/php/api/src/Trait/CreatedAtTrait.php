<?php

namespace Lychen\Api\Trait;

/**
 * The creation date of an API resource class, mapped from its entity's (see
 * Lychen\UtilModel\Trait\CreatedAtTrait on the Doctrine side).
 */
trait CreatedAtTrait
{
    public ?\DateTimeImmutable $createdAt = null;
}
