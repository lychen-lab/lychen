<?php

namespace App\Api\Resource\AreaActivity;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Resource\AreaActivity\Dto\AreaActivityCollection;
use App\Api\Resource\AreaActivity\Dto\AreaActivityPatch;
use App\Api\Resource\AreaActivity\Dto\AreaActivityPost;
use App\Api\Trait\CreatedAtTrait;
use App\Api\Trait\UuidIdentifierTrait;

#[ApiResource(
    stateOptions: new Options(entityClass: \App\Entity\AreaActivity::class)
)]
#[Get()]
#[GetCollection(
    output: AreaActivityCollection::class,
)]
#[Post(input: AreaActivityPost::class)]
#[Patch(input: AreaActivityPatch::class)]
#[Delete()]
final class AreaActivity
{
    use UuidIdentifierTrait;
    use CreatedAtTrait;
}
