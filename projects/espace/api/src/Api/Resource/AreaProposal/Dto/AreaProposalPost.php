<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Api\Transform\ActivityCodesToActivities;
use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: AreaProposal::class)]
final class AreaProposalPost
{
    public Uuid $uuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    public string $description;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $surfaceTotal;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $surfaceToShare;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $city;

    #[Assert\NotNull]
    public int $altitude;

    /**
     * @var list<string>
     */
    #[Map(transform: ActivityCodesToActivities::class)]
    public array $activities = [];
}
