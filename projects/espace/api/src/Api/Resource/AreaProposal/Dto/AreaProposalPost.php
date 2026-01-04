<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Map(target: AreaProposal::class)]
final class AreaProposalPost
{
    use UuidIdentifierTrait;

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
}
