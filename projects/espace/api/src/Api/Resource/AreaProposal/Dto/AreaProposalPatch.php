<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: AreaProposal::class)]
final class AreaProposalPatch
{
    public ?string $title;
    public ?string $description;
    public ?int $surfaceTotal;
    public ?int $surfaceToShare;
    public ?string $city;
    public ?int $altitude;
}
