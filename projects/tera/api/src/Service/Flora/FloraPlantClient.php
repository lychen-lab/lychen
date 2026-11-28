<?php

namespace App\Service\Flora;

use App\ApiResource\Plant;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * The only class that talks to Flora's plants: the seam the tests replace with a mock,
 * so that they test tera's API and never call Flora.
 *
 * Not final: PHPUnit cannot double a final class.
 */
readonly class FloraPlantClient
{
    /**
     * @param HttpClientInterface $floraClient the `flora.client` scoped client (framework.php)
     */
    public function __construct(private HttpClientInterface $floraClient)
    {
    }

    /**
     * @return Plant|null null when Flora has no plant of this ULID
     *
     * @throws FloraUnavailableException
     */
    public function find(string $ulid): ?Plant
    {
        try {
            $response = $this->floraClient->request('GET', '/api/plants/'.rawurlencode($ulid));

            if (404 === $response->getStatusCode()) {
                return null;
            }

            $data = $response->toArray();
        } catch (ExceptionInterface $e) {
            throw new FloraUnavailableException(\sprintf('Flora did not give plant %s.', $ulid), previous: $e);
        }

        if (!\is_string($data['name'] ?? null)) {
            throw new FloraUnavailableException(\sprintf('Flora gave plant %s without a name.', $ulid));
        }

        return new Plant(
            ulid: $ulid,
            name: $data['name'],
            melliferous: true === ($data['melliferous'] ?? false),
            medicinal: true === ($data['medicinal'] ?? false),
        );
    }
}
