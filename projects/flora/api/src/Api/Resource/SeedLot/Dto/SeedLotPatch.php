<?php

namespace App\Api\Resource\SeedLot\Dto;

use App\Entity\SeedLot;
use Lychen\Api\Transform\UuidToEntity;
use Lychen\Api\Validator\UuidExists;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Condition\IsNotNull;
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
 * - "characteristics": ["<uuid>", ...]: replaces the whole list, [] unlinks them all, null
 *   leaves them as they are.
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
     * Nullable so that an explicit null is accepted, and IsNotNull maps it like an absent
     * property: the characteristics are left as they are. [] is mapped, and unlinks them
     * all. #[UuidExists] skips null too.
     *
     * @var list<Uuid>|null
     */
    #[UuidExists]
    #[Map(if: new IsNotNull(), transform: UuidToEntity::class)]
    public ?array $characteristics;
}
