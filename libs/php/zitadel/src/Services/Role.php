<?php

namespace Lychen\UtilZitadelBundle\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class Role
{
    public function __construct(private HttpClientInterface $client, private string $domain, private string $pat)
    {
    }

    public function create(string $projectId, array $attributes): array
    {
        $response = $this->client->request(
            'POST',
            $this->domain.'/management/v1/projects/'.$projectId.'/roles',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$this->pat,
                ],
                'json' => $attributes,
            ]
        );

        return $response->toArray();
    }

    public function search(string $projectId, string $roleKey): array
    {
        $response = $this->client->request(
            'POST',
            $this->domain.'/management/v1/projects/'.$projectId.'/roles/_search',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$this->pat,
                ],
                'json' => [
                    'queries' => [[
                        'keyQuery' => [
                            'key' => $roleKey,
                            'method' => 'TEXT_QUERY_METHOD_EQUALS',
                        ],
                    ]],
                ],
            ]
        );

        return $response->toArray();
    }
}
