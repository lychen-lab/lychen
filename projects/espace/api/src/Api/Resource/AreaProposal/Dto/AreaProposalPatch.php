<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: AreaProposal::class)]
final class AreaProposalPatch
{
    use UuidIdentifierTrait;

    public ?string $title;
    public ?string $description;
    public ?int $surfaceTotal;
    public ?int $surfaceToShare;
    public ?string $city;
    public ?int $altitude;
}
