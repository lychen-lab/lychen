<?php

namespace App\Api\Resource\Characteristic\Dto;

use App\Entity\Characteristic;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Body of POST /api/characteristics. The uniqueness of the code is checked on the
 * entity (#[UniqueEntity]), once mapped.
 */
#[Map(target: Characteristic::class)]
final class CharacteristicPost
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[a-z0-9_]+$/', message: 'Use lowercase letters, digits and underscores only.')]
    public string $code;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $label;
}
