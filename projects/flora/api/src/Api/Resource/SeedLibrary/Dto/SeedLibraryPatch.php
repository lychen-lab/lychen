<?php

namespace App\Api\Resource\SeedLibrary\Dto;

use App\Entity\SeedLibrary;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Body of PATCH /api/seed_libraries/{uuid} (application/merge-patch+json).
 *
 * The properties are deliberately left uninitialized: only the fields sent by the client
 * get initialized and the object mapper skips the others, which gives merge-patch
 * semantics. A non-nullable type rejects an explicit null ($name), a nullable one
 * accepts it to clear the field ($city). The constraints allow null: the validator reads
 * an uninitialized property as null.
 */
#[Map(target: SeedLibrary::class)]
final class SeedLibraryPatch
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Length(max: 255)]
    public ?string $city;
}
