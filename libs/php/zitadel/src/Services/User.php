<?php

namespace Lychen\UtilZitadelBundle\Services;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class User
{
    public function __construct(private HttpClientInterface $client, private string $domain, private string $pat)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function createHumanUser(array $attributes): array
    {
        $response = $this->client->request(
            'POST',
            $this->domain.'/v2/users/human',
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function searchByEmail(string $email): array
    {
        $response = $this->client->request(
            'POST',
            $this->domain.'/v2/users',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$this->pat,
                ],
                'json' => [
                    'queries' => [[
                        'emailQuery' => [
                            'emailAddress' => $email,
                            'method' => 'TEXT_QUERY_METHOD_EQUALS',
                        ],
                    ]],
                ],
            ]
        );

        return $response->toArray()['result'][0];
    }

    public function addUserGrants(string $userId, string $projectId, array $attributes): array
    {
        $response = $this->client->request(
            'POST',
            $this->domain.'/management/v1/users/'.$userId.'/grants',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$this->pat,
                ],
                'json' => [...$attributes, 'projectId' => $projectId],
            ]
        );

        return $response->toArray();
    }
}
