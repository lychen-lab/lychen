<?php

namespace Lychen\Api\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Uid\Uuid;

/**
 * The identifier of an API resource class: its entity's uuid, never its int id
 * (/api/<resources>/{uuid}).
 */
trait UuidIdentifierTrait
{
    #[ApiProperty(identifier: true)]
    public ?Uuid $uuid = null;
}
