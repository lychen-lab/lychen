<?php

namespace App\Api\Resource\AreaProposal\Endpoint;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\ObjectMappedItemProvider;
use App\Api\Resource\AreaProposal\AreaProposalResource;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaProposal;

#[ApiResource(
    shortName: AreaProposalResource::SHORT_NAME,
)]
#[Get(
    provider: ObjectMappedItemProvider::class,
    stateOptions: new Options(entityClass: AreaProposal::class),
)]
class AreaProposalGet
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
    use PlaceTrait;
}
