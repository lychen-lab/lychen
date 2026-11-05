<?php

namespace App\Api\Resource\SeedLot;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Resource\SeedLot\Dto\SeedLotPatch;
use App\Api\Resource\SeedLot\Dto\SeedLotPost;
use App\Api\Transform\RelationToUuid;
use App\Entity\SeedLot as SeedLotEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;

/**
 * The API representation of a SeedLot entity (see SeedLibrary for the read/write flow).
 *
 * Its relations are exposed by uuid, not by IRI nor as nested resources:
 *
 *     {
 *       "@id": "/api/seed_lots/0199...",
 *       "uuid": "0199...",
 *       "seedLibrary": "0199...",
 *       "characteristics": ["0199...", "0199..."]
 *     }
 *
 * and written the same way, through SeedLotPost and SeedLotPatch.
 */
#[ApiResource(
    stateOptions: new Options(entityClass: SeedLotEntity::class),
)]
#[Get]
#[GetCollection]
#[Post(input: SeedLotPost::class, collectDenormalizationErrors: true)]
#[Patch(input: SeedLotPatch::class, collectDenormalizationErrors: true)]
#[Delete(map: false)]
#[Map(source: SeedLotEntity::class)]
final class SeedLot
{
    #[ApiProperty(identifier: true)]
    public ?Uuid $uuid = null;

    public ?string $variety = null;

    public ?int $harvestYear = null;

    public ?int $quantityInGrams = null;

    /**
     * ManyToOne: the uuid of the library keeping the lot.
     */
    #[Map(source: 'seedLibrary', transform: [RelationToUuid::class, 'one'])]
    public ?Uuid $seedLibrary = null;

    /**
     * ManyToMany: the uuids of the lot's characteristics.
     *
     * @var list<Uuid>
     */
    #[Map(source: 'characteristics', transform: [RelationToUuid::class, 'many'])]
    public array $characteristics = [];

    public ?\DateTimeImmutable $createdAt = null;

    public ?\DateTimeInterface $updatedAt = null;
}
