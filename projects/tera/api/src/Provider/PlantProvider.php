<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Plant;
use App\Service\Flora\FloraPlantClient;
use App\Service\Flora\FloraUnavailableException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Reads a Plant from Flora. Returning null lets API Platform answer 404; Flora being
 * down is tera's 503.
 *
 * @implements ProviderInterface<Plant>
 */
final readonly class PlantProvider implements ProviderInterface
{
    public function __construct(private FloraPlantClient $floraPlantClient)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Plant
    {
        try {
            return $this->floraPlantClient->find((string) $uriVariables['ulid']);
        } catch (FloraUnavailableException $e) {
            throw new ServiceUnavailableHttpException(null, 'Flora is unavailable, try again later.', $e);
        }
    }
}
