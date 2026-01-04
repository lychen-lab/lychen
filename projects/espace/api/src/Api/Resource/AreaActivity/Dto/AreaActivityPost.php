<?php

namespace App\Api\Resource\AreaActivity\Dto;

use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaActivity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Map(target: AreaActivity::class)]
final class AreaActivityPost
{
    use UuidIdentifierTrait;

    #[NotBlank]
    public ?string $code;
}
