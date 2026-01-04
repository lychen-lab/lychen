<?php

namespace App\Api\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Uid\Uuid;

trait UuidIdentifierTrait
{
    #[ApiProperty(identifier: true)]
    public ?Uuid $uuid;
}
