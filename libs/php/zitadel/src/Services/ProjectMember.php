<?php

namespace Lychen\UtilZitadelBundle\Services;

use JetBrains\PhpStorm\Deprecated;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ProjectMember
{
    public function __construct(private HttpClientInterface $client, private string $domain, private string $pat)
    {
    }

    #[Deprecated('Not working, auth error')]
    public function update(string $projectId, string $userId, array $attributes): array
    {
        $response = $this->client->request(
            'PUT',
            $this->domain.'/management/v1/projects/'.$projectId.'/members/'.$userId,
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
}
