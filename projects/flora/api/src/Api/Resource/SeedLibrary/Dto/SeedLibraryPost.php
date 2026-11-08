<?php

namespace App\Api\Resource\SeedLibrary\Dto;

use App\Entity\SeedLibrary;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Body of POST /api/seed_libraries. The object mapper creates the entity and copies the
 * properties sharing a name with it (through its setters).
 */
#[Map(target: SeedLibrary::class)]
final class SeedLibraryPost
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Length(max: 255)]
    public ?string $city = null;
}
