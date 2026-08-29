<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Api\Resource\AreaProposal\ProposerTrait;
use App\Api\Trait\ActivitiesAsStringTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: AreaProposal::class)]
final class AreaProposalCollection
{
    use UuidIdentifierTrait;
    use PlaceTrait;
    use ActivitiesAsStringTrait;
    use ProposerTrait;

    public ?string $title;
    public ?string $description;
    public ?int $surfaceToShare;
    public ?string $city;
    public ?int $altitude;
}
