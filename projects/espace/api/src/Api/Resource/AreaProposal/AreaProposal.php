<?php

namespace App\Api\Resource\AreaProposal;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Filter\PlaceFilter;
use App\Api\Resource\AreaProposal\Dto\AreaProposalCollection;
use App\Api\Resource\AreaProposal\Dto\AreaProposalPatch;
use App\Api\Resource\AreaProposal\Dto\AreaProposalPost;
use App\Api\Resource\AreaProposal\Processor\AreaProposalPatchProcessor;
use App\Api\Resource\AreaProposal\Processor\AreaProposalPostProcessor;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaProposal as AreaProposalEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    stateOptions: new Options(entityClass: AreaProposalEntity::class),
)]
#[Get()]
#[GetCollection(
    output: AreaProposalCollection::class,
    parameters: [
        'place' => new QueryParameter(
            filter: new PlaceFilter(),
        )
    ],
)]
#[Post(
    input: AreaProposalPost::class,
    processor: AreaProposalPostProcessor::class
)]
#[Patch(
    input: AreaProposalPatch::class,
    processor: AreaProposalPatchProcessor::class
)]
#[Delete]
#[Map(source: AreaProposalEntity::class)]
final class AreaProposal
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use PlaceTrait;
    use ActivitiesAsStringTrait;

    public ?string $title;
    public ?string $description;
    public ?string $archivedAt;
    public ?int $surfaceTotal;
    public ?int $surfaceToShare;

    public ?string $city;
    public ?int $altitude;
}
