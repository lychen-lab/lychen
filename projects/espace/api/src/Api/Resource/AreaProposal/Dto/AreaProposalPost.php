<?php

namespace App\Api\Resource\AreaProposal\Dto;

use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AreaProposalPost
{
    #[Map()]
    public ?Uuid $uuid;

    #[NotBlank]
    #[Map()]
    public ?string $title;

    #[NotBlank]
    #[Map()]
    public ?string $description;

    #[NotBlank]
    #[Map()]
    public ?int $surfaceTotal;

    #[NotBlank]
    #[Map()]
    public ?int $surfaceToShare;

    #[NotBlank]
    #[Map()]
    public ?string $city;

    #[NotBlank]
    #[Map()]
    public ?int $altitude;

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
