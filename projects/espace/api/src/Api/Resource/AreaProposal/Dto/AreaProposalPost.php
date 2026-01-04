<?php

namespace App\Api\Resource\AreaProposal\Dto;

use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AreaProposalPost
{
    public ?Uuid $uuid;

    #[NotBlank]
    public ?string $title;

    #[NotBlank]
    public ?string $description;

    #[NotBlank]
    public ?int $surfaceTotal;

    #[NotBlank]
    public ?int $surfaceToShare;

    #[NotBlank]
    public ?string $city;

    #[NotBlank]
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
