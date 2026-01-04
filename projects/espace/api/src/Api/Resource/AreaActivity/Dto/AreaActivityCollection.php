<?php

namespace App\Api\Resource\AreaActivity\Dto;

use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaActivity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: AreaActivity::class)]
final class AreaActivityCollection
{
    use UuidIdentifierTrait;

    public ?string $code;
}
