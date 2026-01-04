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
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;

#[ApiResource(
    stateOptions: new Options(entityClass: \App\Entity\AreaProposal::class),
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
#[Post(input: AreaProposalPost::class)]
#[Patch(input: AreaProposalPatch::class)]
#[Delete()]
final class AreaProposal
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use PlaceTrait;

    public ?string $title;
    public ?string $description;
    public ?string $archivedAt;
    public ?int $surfaceTotal;
    public ?int $surfaceToShare;

    public ?string $city;
    public ?int $altitude;
}
