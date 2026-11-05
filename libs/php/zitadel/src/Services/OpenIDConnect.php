<?php

namespace Lychen\UtilZitadelBundle\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class OpenIDConnect
{
    public function __construct(private HttpClientInterface $client, private string $domain, private string $clientId, private string $clientSecret)
    {
    }

    public function introspectToken(string $token, string $scope): array
    {
        $response = $this->client->request(
            'POST',
            $this->domain.'/oauth/v2/introspect',
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret),
                ],
                'body' => [
                    'token' => $token,
                ],
                'query' => [
                    'scope' => $scope,
                ],
            ]
        );

        return $response->toArray();
    }

    public function userinfo(string $token)
    {
        $response = $this->client->request(
            'GET',
            $this->domain.'/oidc/v1/userinfo',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => [
                    'scope' => 'openid email profile urn:zitadel:iam:org:project:id:zitadel:aud urn:zitadel:iam:org:project:roles',
                ],
            ]
        );

        return $response->toArray();
    }
}
