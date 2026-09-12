<?php

namespace App\Api\Resource\AreaRequest\Dto;

use Symfony\Component\ObjectMapper\Attribute\Map;

final class AreaRequestPatch
{
    public ?string $title;
    public ?string $description;
    public ?int $minimalSurfaceRequested;
    public ?string $city;

    /**
     * @var string[]
     */
    #[Map(if: false)]
    public ?array $activities;
}
