<?php

namespace App\Api\Resource\SeedLot\Dto;

use App\Api\Transform\UuidToCharacteristic;
use App\Api\Transform\UuidToSeedLibrary;
use App\Entity\Characteristic;
use App\Entity\SeedLibrary;
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
 * one is a 422), #[UuidExists] checks the entities exist, and the transforms of #[Map]
 * resolve them into the entities to link.
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
    #[UuidExists(SeedLibrary::class)]
    #[Map(transform: UuidToSeedLibrary::class)]
    public Uuid $seedLibrary;

    /**
     * @var list<Uuid>
     */
    #[UuidExists(Characteristic::class)]
    #[Map(transform: UuidToCharacteristic::class)]
    public array $characteristics = [];
}
