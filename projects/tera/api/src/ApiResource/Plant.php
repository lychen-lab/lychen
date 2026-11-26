<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Provider\PlantProvider;

/**
 * A plant of Flora's catalogue, as tera exposes it: not stored here, read from Flora on
 * every request (PlantProvider → FloraPlantClient).
 */
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/plants/{ulid}', provider: PlantProvider::class),
    ],
)]
final readonly class Plant
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $ulid,
        public string $name,
        public bool $melliferous,
        public bool $medicinal,
    ) {
    }
}
