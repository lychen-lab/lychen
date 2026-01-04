<?php

namespace App\Api\Resource\AreaActivity\Dto;

use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaActivity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: AreaActivity::class)]
final class AreaActivityPatch
{
    use UuidIdentifierTrait;

    public ?string $code;
}
