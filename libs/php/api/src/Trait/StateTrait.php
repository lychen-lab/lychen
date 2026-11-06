<?php

namespace Lychen\Api\Trait;

/**
 * The workflow state of an API resource class, mapped from its entity's.
 */
trait StateTrait
{
    public ?string $state = null;
}
