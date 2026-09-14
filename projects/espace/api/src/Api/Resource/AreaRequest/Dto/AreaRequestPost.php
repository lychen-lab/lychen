<?php

namespace App\Api\Resource\AreaRequest\Dto;

use App\Api\Transform\ActivityCodesToActivities;
use App\Entity\AreaRequest;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: AreaRequest::class)]
final class AreaRequestPost
{
    public Uuid $uuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    public string $description;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $minimalSurfaceRequested;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $city;

    /**
     * @var list<string>
     */
    #[Map(transform: ActivityCodesToActivities::class)]
    public array $activities = [];
}
