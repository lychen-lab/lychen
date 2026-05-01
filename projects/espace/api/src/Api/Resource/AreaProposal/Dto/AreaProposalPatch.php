<?php

namespace App\Api\Resource\AreaProposal\Dto;

use Symfony\Component\ObjectMapper\Attribute\Map;

final class AreaProposalPatch
{
    public ?string $title;
    public ?string $description;
    public ?int $surfaceTotal;
    public ?int $surfaceToShare;
    public ?string $city;
    public ?int $altitude;
    public ?string $soilType;
    public ?string $availableAt;

    /**
     * @var string[]
     */
    #[Map(if: false)]
    public ?array $activities;
}
