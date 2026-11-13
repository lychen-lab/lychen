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
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPatch;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPost;
use App\Api\Resource\AreaRequest\Dto\AreaRequestReject;
use App\Api\State\DraftContentProcessor;
use App\Api\Trait\ActivitiesAsStringTrait;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Api\Transform\PersonName;
use App\Api\Workflow\WorkflowDiagram;
use App\Api\Workflow\WorkflowDiagramProvider;
use App\Doctrine\Filter\MineFilter;
use App\Entity\AreaRequest as AreaRequestEntity;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Lychen\Api\State\WorkflowTransitionProcessor;
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
// Keep it after the plain Get, which IRIs are generated from.
#[Get(
    uriTemplate: '/area_requests/{uuid}/workflow',
    openapi: new OpenApiOperation(summary: 'Retrieves the Mermaid diagram of the AreaRequest workflow, current place outlined.'),
    output: WorkflowDiagram::class,
    provider: WorkflowDiagramProvider::class,
    map: false,
)]
#[GetCollection(
    parameters: [
        'place' => new QueryParameter(
            schema: ['type' => 'string', 'enum' => AreaRequestWorkflow::PLACES],
            filter: new ExactFilter(),
            property: 'place',
            description: 'Filter by workflow place',
        ),
        'mine' => new QueryParameter(
            schema: ['type' => 'boolean'],
            filter: MineFilter::class,
            property: 'requester',
            description: 'true: only the current user\'s own, whatever their place',
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
// Only a draft can be edited (409 otherwise).
#[Patch(input: AreaRequestPatch::class, collectDenormalizationErrors: true, processor: DraftContentProcessor::class)]
// One PATCH per workflow transition. map: false hands the entity itself to the processor,
// which applies the transition and answers with this resource mapped from it (409 when
// the current place does not allow it). Who reaches which transition is decided by
// AreaVisibilityExtension: the owner submits and archives, moderators decide.
#[Patch(
    uriTemplate: '/area_requests/{uuid}/'.AreaRequestWorkflow::TRANSITION_SUBMIT,
    openapi: new OpenApiOperation(summary: 'Submits the draft AreaRequest to moderation (owner only).'),
    options: ['transition' => AreaRequestWorkflow::TRANSITION_SUBMIT],
    input: false,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
#[Patch(
    uriTemplate: '/area_requests/{uuid}/'.AreaRequestWorkflow::TRANSITION_APPROVE,
    openapi: new OpenApiOperation(summary: 'Activates the AreaRequest waiting for validation (moderators only).'),
    options: ['transition' => AreaRequestWorkflow::TRANSITION_APPROVE],
    input: false,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
#[Patch(
    uriTemplate: '/area_requests/{uuid}/'.AreaRequestWorkflow::TRANSITION_REJECT,
    openapi: new OpenApiOperation(summary: 'Sends the AreaRequest back to its owner as a draft, with the reason (moderators only).'),
    options: ['transition' => AreaRequestWorkflow::TRANSITION_REJECT],
    input: AreaRequestReject::class,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
#[Patch(
    uriTemplate: '/area_requests/{uuid}/'.AreaRequestWorkflow::TRANSITION_ARCHIVE,
    openapi: new OpenApiOperation(summary: 'Archives the AreaRequest (owner only).'),
    options: ['transition' => AreaRequestWorkflow::TRANSITION_ARCHIVE],
    input: false,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
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

    /**
     * Why moderation sent the listing back. Only ever set on a draft, which nobody but
     * its owner can read (AreaVisibilityExtension), and cleared when it is submitted again.
     */
    public ?string $rejectionReason = null;
    public ?int $minimalSurfaceRequested = null;
    public ?string $city = null;

    #[Map(source: 'requester', transform: [PersonName::class, 'givenName'])]
    public ?string $requesterFirstName = null;

    #[Map(source: 'requester', transform: [PersonName::class, 'familyName'])]
    public ?string $requesterLastName = null;
}
