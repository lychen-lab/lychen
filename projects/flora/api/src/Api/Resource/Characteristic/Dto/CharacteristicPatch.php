<?php

namespace App\Api\Resource\Characteristic\Dto;

use App\Entity\Characteristic;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Body of PATCH /api/characteristics/{uuid} (application/merge-patch+json): uninitialized
 * properties for merge-patch semantics, see SeedLibraryPatch.
 */
#[Map(target: Characteristic::class)]
final class CharacteristicPatch
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[a-z0-9_]+$/', message: 'Use lowercase letters, digits and underscores only.')]
    public string $code;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public string $label;
}
