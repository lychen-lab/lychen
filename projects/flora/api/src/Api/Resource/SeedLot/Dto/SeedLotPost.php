<?php

namespace App\Api\Resource\SeedLot\Dto;

use App\Api\Transform\UuidToEntity;
use App\Entity\SeedLot;
use App\Validator\UuidExists;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Body of POST /api/seed_lots.
 *
 *     {
 *       "variety": "Tomate cœur de bœuf",
 *       "harvestYear": 2026,
 *       "quantityInGrams": 40,
 *       "seedLibrary": "0199...",
 *       "characteristics": ["0199...", "0199..."]
 *     }
 *
 * The relations take uuids. The serializer turns each string into a Uuid (a malformed
 * one is a 422), #[UuidExists] checks the entities exist, and UuidToEntity resolves them
 * into the entities to link. Neither names the entity: both read it from the Doctrine
 * association the property is mapped onto (SeedLot::$seedLibrary, ::$characteristics).
 */
#[Map(target: SeedLot::class)]
final class SeedLotPost
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $variety;

    #[Assert\Range(min: 1900, max: 2100)]
    public ?int $harvestYear = null;

    #[Assert\PositiveOrZero]
    public int $quantityInGrams = 0;

    #[Assert\NotNull]
    #[UuidExists]
    #[Map(transform: UuidToEntity::class)]
    public Uuid $seedLibrary;

    /**
     * @var list<Uuid>
     */
    #[UuidExists]
    #[Map(transform: UuidToEntity::class)]
    public array $characteristics = [];
}
