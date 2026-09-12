<?php

namespace App\Api\Resource\AreaRequest\Dto;

use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AreaRequestPost
{
    public ?Uuid $uuid;

    #[NotBlank]
    public ?string $title;

    #[NotBlank]
    public ?string $description;

    #[NotBlank]
    public ?int $minimalSurfaceRequested;

    #[NotBlank]
    public ?string $city;

    /**
     * @var string[]
     */
    #[Map(if: false)]
    public ?array $activities;

    public function __construct()
    {
        $this->activities = [];
    }
}
