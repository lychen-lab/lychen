<?php

namespace App\Api\Resource\AreaRequest;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Filter\PlaceFilter;
use App\Api\Resource\AreaRequest\Dto\AreaRequestCollection;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPatch;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPost;
use App\Api\Resource\AreaRequest\Processor\AreaRequestPatchProcessor;
use App\Api\Resource\AreaRequest\Processor\AreaRequestPostProcessor;
use App\Api\Trait\ActivitiesAsStringTrait;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaRequest as AreaRequestEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    stateOptions: new Options(entityClass: AreaRequestEntity::class),
)]
#[Get()]
#[GetCollection(
    output: AreaRequestCollection::class,
    parameters: [
        'place' => new QueryParameter(
            filter: new PlaceFilter(),
        ),
    ],
)]
#[Post(
    input: AreaRequestPost::class,
    processor: AreaRequestPostProcessor::class
)]
#[Patch(
    input: AreaRequestPatch::class,
    processor: AreaRequestPatchProcessor::class
)]
#[Delete]
#[Map(source: AreaRequestEntity::class)]
final class AreaRequest
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use PlaceTrait;
    use ActivitiesAsStringTrait;
    use RequesterTrait;

    public ?string $title;
    public ?string $description;
    public ?string $archivedAt;
    public ?int $minimalSurfaceRequested;
    public ?string $city;
}
