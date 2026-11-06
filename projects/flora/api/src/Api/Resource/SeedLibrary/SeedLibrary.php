<?php

namespace App\Api\Resource\SeedLibrary;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Resource\SeedLibrary\Dto\SeedLibraryPatch;
use App\Api\Resource\SeedLibrary\Dto\SeedLibraryPost;
use App\Entity\SeedLibrary as SeedLibraryEntity;
use Lychen\Api\Trait\CreatedAtTrait;
use Lychen\Api\Trait\UpdatedAtTrait;
use Lychen\Api\Trait\UuidIdentifierTrait;
use Lychen\Api\Transform\RelationToUuid;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;

/**
 * The API representation of a SeedLibrary entity, separate from the Doctrine model.
 *
 * - Reads (Get, GetCollection): the Doctrine provider loads the entity (stateOptions),
 *   then the object mapper maps it onto this class (#[Map(source: ...)]).
 * - Writes (Post, Patch): the body is deserialized into the operation's input DTO,
 *   validated, mapped onto the entity (the DTO's #[Map(target: ...)]), validated again,
 *   persisted, and mapped back onto this class for the response.
 *
 * The resource is identified by its uuid (/api/seed_libraries/{uuid}); the entity's
 * int id never leaves the database. Keep this the only class mapped from the entity:
 * Symfony registers a single reverse mapping per source class.
 */
#[ApiResource(
    stateOptions: new Options(entityClass: SeedLibraryEntity::class),
)]
#[Get]
#[GetCollection]
#[Post(input: SeedLibraryPost::class, collectDenormalizationErrors: true)]
#[Patch(input: SeedLibraryPatch::class, collectDenormalizationErrors: true)]
// A deletion needs no representation: skip mapping the entity to the resource and back.
#[Delete(map: false)]
#[Map(source: SeedLibraryEntity::class)]
final class SeedLibrary
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;

    public ?string $name = null;

    public ?string $city = null;

    /**
     * The uuids of the library's lots. Read-only here: SeedLot owns the relation, a lot
     * moves to another library through PATCH /api/seed_lots/{uuid} {"seedLibrary": uuid}.
     *
     * @var list<Uuid>
     */
    #[Map(source: 'seedLots', transform: [RelationToUuid::class, 'many'])]
    public array $seedLots = [];
}
