<?php

namespace Lychen\Api\Trait;

/**
 * The last update date of an API resource class, mapped from its entity's (see
 * Lychen\UtilModel\Trait\UpdatedAtTrait on the Doctrine side).
 */
trait UpdatedAtTrait
{
    public ?\DateTimeInterface $updatedAt = null;
}
