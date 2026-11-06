<?php

namespace App\Api\Resource\Characteristic;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Resource\Characteristic\Dto\CharacteristicPatch;
use App\Api\Resource\Characteristic\Dto\CharacteristicPost;
use App\Entity\Characteristic as CharacteristicEntity;
use Lychen\Api\Trait\CodeTrait;
use Lychen\Api\Trait\CreatedAtTrait;
use Lychen\Api\Trait\UpdatedAtTrait;
use Lychen\Api\Trait\UuidIdentifierTrait;
use Symfony\Component\ObjectMapper\Attribute\Map;

/**
 * The API representation of a Characteristic entity (see SeedLibrary for the read/write
 * flow). It has no relation of its own: seed lots link it through their
 * "characteristics" list of uuids.
 */
#[ApiResource(
    stateOptions: new Options(entityClass: CharacteristicEntity::class),
)]
#[Get]
#[GetCollection]
#[Post(input: CharacteristicPost::class, collectDenormalizationErrors: true)]
#[Patch(input: CharacteristicPatch::class, collectDenormalizationErrors: true)]
#[Delete(map: false)]
#[Map(source: CharacteristicEntity::class)]
final class Characteristic
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use CodeTrait;

    public ?string $label = null;
}
