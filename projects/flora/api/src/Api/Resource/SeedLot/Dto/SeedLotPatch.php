<?php

namespace App\Api\Resource\SeedLot\Dto;

use App\Api\Transform\UuidToEntity;
use App\Entity\SeedLot;
use App\Validator\UuidExists;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Body of PATCH /api/seed_lots/{uuid} (application/merge-patch+json).
 *
 * Like SeedLibraryPatch, the properties are left uninitialized: the object mapper skips
 * the ones the client did not send, the relations included (symfony/object-mapper 8.1.9+
 * checks a property carrying a #[Map] is initialized before reading it). For a relation:
 *
 * - absent: left as is;
 * - "seedLibrary": "<uuid>": the lot moves to that library (null is a 422: required);
 * - "characteristics": ["<uuid>", ...]: replaces the whole list, [] or null unlinks them
 *   all.
 */
#[Map(target: SeedLot::class)]
final class SeedLotPatch
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public string $variety;

    #[Assert\Range(min: 1900, max: 2100)]
    public ?int $harvestYear;

    #[Assert\PositiveOrZero]
    public int $quantityInGrams;

    #[UuidExists]
    #[Map(transform: UuidToEntity::class)]
    public Uuid $seedLibrary;

    /**
     * Nullable for the serializer, never null once set: in a JSON merge patch (RFC 7396)
     * null removes the member, which for a list of links means unlinking them all. The
     * hook stores it as [], so the validator and UuidToEntity only ever see a list.
     *
     * @var list<Uuid>|null
     */
    #[UuidExists]
    #[Map(transform: UuidToEntity::class)]
    public ?array $characteristics {
        set => $value ?? [];
    }
}
