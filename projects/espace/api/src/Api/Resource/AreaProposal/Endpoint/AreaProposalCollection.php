<?php

namespace App\Api\Resource\AreaProposal\Endpoint;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Filter\PlaceFilter;
use App\Api\Provider\ObjectMappedCollectionProvider;
use App\Api\Resource\AreaProposal\AreaProposalResource;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaProposal;

#[ApiResource(
    shortName: AreaProposalResource::SHORT_NAME,
)]
#[GetCollection(
    #normalizationContext: ['groups' => ['area_proposal:collection']],
    provider: ObjectMappedCollectionProvider::class,
    stateOptions: new Options(entityClass: AreaProposal::class),
    parameters: [
        'place' => new QueryParameter(
            filter: new PlaceFilter(),
        )
    ],
)]
class AreaProposalCollection
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use PlaceTrait;
}
