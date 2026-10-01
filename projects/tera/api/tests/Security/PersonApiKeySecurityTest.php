<?php

namespace App\Tests\Security;

use App\Security\Authenticator\PersonApiKeyAuthenticator;
use App\Security\Voter\LandVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class PersonApiKeySecurityTest extends AbstractApiTestCase
{
    public function testAuthentication(): void
    {
        $person = $this->createPerson();

        $response = $this->browser()->actingAs($person)
            ->post('/api/person_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => [LandVoter::POST, LandVoter::COLLECTION],
                ]])
            ->assertSuccessful()
            ->json()->decoded();

        $this->browser()->setDefaultHttpOptions(
            [
                'headers' => [
                    PersonApiKeyAuthenticator::HEADER_ATTRIBUTE => $response['token'],
                ],
            ]
        )->get('/api/lands')->assertSuccessful();
    }

    public function testAuthenticationWithInvalidToken(): void
    {
        $this->browser()->setDefaultHttpOptions(
            [
                'headers' => [
                    PersonApiKeyAuthenticator::HEADER_ATTRIBUTE => 'invalid-token',
                ],
            ]
        )->get('/api/lands')->assertStatus(401);
    }

    public function testCannotCreateApiKeyThroughApi(): void
    {
        $context = $this->createLandContext();
        $apiKey = $this->createPersonApiKey($context->owner,
            ['permissions' => ['person:person_api_key:post']]);

        $landApiKey = $this->createLandApiKey($context->land,
            ['permissions' => ['person:person_api_key:post']]);

        $this->browser()->actingAs($apiKey)
            ->post('/api/person_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => ['person:land:post', 'person:land:collection'],
                ]])->assertStatus(403);

        $this->browser()->actingAs($landApiKey)
            ->post('/api/person_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => ['person:land:post', 'person:land:collection'],
                ]])->assertStatus(403);
    }
}
