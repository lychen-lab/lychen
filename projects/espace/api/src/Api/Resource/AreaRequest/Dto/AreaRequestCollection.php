<?php

namespace App\Api\Resource\AreaRequest\Dto;

use App\Api\Resource\AreaRequest\RequesterTrait;
use App\Api\Trait\ActivitiesAsStringTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaRequest;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: AreaRequest::class)]
final class AreaRequestCollection
{
    use UuidIdentifierTrait;
    use PlaceTrait;
    use ActivitiesAsStringTrait;
    use RequesterTrait;

    public ?string $title;
    public ?string $description;
    public ?int $minimalSurfaceRequested;
    public ?string $city;
}
