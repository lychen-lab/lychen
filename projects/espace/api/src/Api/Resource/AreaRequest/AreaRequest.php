<?php

namespace App\Api\Resource\AreaRequest;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\SortFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPatch;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPost;
use App\Api\Trait\ActivitiesAsStringTrait;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Api\Transform\PersonName;
use App\Entity\AreaRequest as AreaRequestEntity;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Symfony\Component\ObjectMapper\Attribute\Map;

/**
 * Keep this the only class mapped from the AreaRequest entity: Symfony registers a
 * single reverse mapping per source class, so property-level #[Map] attributes of a
 * second DTO would be ignored. Visibility is enforced by AreaVisibilityExtension.
 */
#[ApiResource(
    stateOptions: new Options(entityClass: AreaRequestEntity::class),
)]
#[Get]
#[GetCollection(
    parameters: [
        'place' => new QueryParameter(
            schema: ['type' => 'string', 'enum' => AreaRequestWorkflow::PLACES],
            filter: new ExactFilter(),
            property: 'place',
            description: 'Filter by workflow place',
        ),
        'activity' => new QueryParameter(
            filter: new ExactFilter(),
            property: 'activities.code',
            description: 'Filter by activity code',
        ),
        'city' => new QueryParameter(
            filter: new PartialSearchFilter(),
            property: 'city',
            description: 'Filter by city (partial, case-insensitive)',
        ),
        'order[createdAt]' => new QueryParameter(
            filter: new SortFilter(),
            property: 'createdAt',
        ),
    ],
)]
#[Post(input: AreaRequestPost::class, collectDenormalizationErrors: true)]
#[Patch(input: AreaRequestPatch::class, collectDenormalizationErrors: true)]
// A deletion needs no representation: skip mapping the entity to the resource and back.
#[Delete(map: false)]
#[Map(source: AreaRequestEntity::class)]
final class AreaRequest
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use PlaceTrait;
    use ActivitiesAsStringTrait;

    public ?string $title = null;
    public ?string $description = null;
    public ?\DateTimeImmutable $archivedAt = null;
    public ?int $minimalSurfaceRequested = null;
    public ?string $city = null;

    #[Map(source: 'requester', transform: [PersonName::class, 'givenName'])]
    public ?string $requesterFirstName = null;

    #[Map(source: 'requester', transform: [PersonName::class, 'familyName'])]
    public ?string $requesterLastName = null;
}
