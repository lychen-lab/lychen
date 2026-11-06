<?php

namespace App\Api\Resource\SeedLot;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Resource\SeedLot\Dto\SeedLotPatch;
use App\Api\Resource\SeedLot\Dto\SeedLotPost;
use App\Entity\SeedLot as SeedLotEntity;
use App\Workflow\SeedLot\SeedLotWorkflow;
use Lychen\Api\State\WorkflowTransitionProcessor;
use Lychen\Api\Trait\CreatedAtTrait;
use Lychen\Api\Trait\StateTrait;
use Lychen\Api\Trait\UpdatedAtTrait;
use Lychen\Api\Trait\UuidIdentifierTrait;
use Lychen\Api\Transform\RelationToUuid;
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
 *
 * Its state follows SeedLotWorkflow, changed through one PATCH per transition
 * (/api/seed_lots/{uuid}/reserve, /release, /give) handled by lychen/api's
 * WorkflowTransitionProcessor: 200 with the lot, or 409 when its state does not allow it.
 */
#[ApiResource(
    stateOptions: new Options(entityClass: SeedLotEntity::class),
)]
#[Get]
#[GetCollection]
#[Post(input: SeedLotPost::class, collectDenormalizationErrors: true)]
#[Patch(input: SeedLotPatch::class, collectDenormalizationErrors: true)]
#[Delete(map: false)]
// One PATCH per workflow transition, with no body. map: false hands the entity itself to the
// processor, which applies the transition and answers with this resource mapped from it.
#[Patch(
    uriTemplate: '/seed_lots/{uuid}/'.SeedLotWorkflow::TRANSITION_RESERVE,
    options: ['transition' => SeedLotWorkflow::TRANSITION_RESERVE],
    input: false,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
#[Patch(
    uriTemplate: '/seed_lots/{uuid}/'.SeedLotWorkflow::TRANSITION_RELEASE,
    options: ['transition' => SeedLotWorkflow::TRANSITION_RELEASE],
    input: false,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
#[Patch(
    uriTemplate: '/seed_lots/{uuid}/'.SeedLotWorkflow::TRANSITION_GIVE,
    options: ['transition' => SeedLotWorkflow::TRANSITION_GIVE],
    input: false,
    map: false,
    processor: WorkflowTransitionProcessor::class,
)]
#[Map(source: SeedLotEntity::class)]
final class SeedLot
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use StateTrait;

    public ?string $variety = null;

    public ?int $harvestYear = null;

    public ?int $quantityInGrams = null;

    /**
     * ManyToOne: the uuid of the library keeping the lot.
     */
    #[Map(source: 'seedLibrary', transform: RelationToUuid::class)]
    public ?Uuid $seedLibrary = null;

    /**
     * ManyToMany: the uuids of the lot's characteristics.
     *
     * @var list<Uuid>
     */
    #[Map(source: 'characteristics', transform: RelationToUuid::class)]
    public array $characteristics = [];
}
