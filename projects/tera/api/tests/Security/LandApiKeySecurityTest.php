<?php

namespace App\Tests\Security;

use App\Security\Authenticator\LandApiKeyAuthenticator;
use App\Security\Voter\LandTaskVoter;
use App\Security\Voter\LandVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandApiKeySecurityTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $this->browser()
            ->post('/api/land_api_keys', ['json' => []])
            ->assertStatus(401);

        $context = $this->createLandContext();
        $context2 = $this->createLandContext();

        $this->browser()->actingAs($context->owner)
            ->post('/api/land_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => [LandTaskVoter::POST, LandTaskVoter::COLLECTION],
                    'land' => $this->getIriFromResource($context2->land),
                ]])
            ->assertStatus(403);
    }

    public function testAuthentication()
    {
        $context = $this->createLandContext();

        $response = $this->browser()->actingAs($context->owner)
            ->post('/api/land_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => [LandVoter::GET, LandTaskVoter::POST, LandTaskVoter::COLLECTION],
                    'land' => $this->getIriFromResource($context->land),
                ]])
            ->assertSuccessful()
            ->json()->decoded();

        $this->browser()->setDefaultHttpOptions(
            [
                'headers' => [
                    LandApiKeyAuthenticator::HEADER_ATTRIBUTE => $response["token"]
                ]
            ]
        )->get($this->getIriFromResource($context->land))->assertSuccessful();
    }

    public function testAuthenticationWithInvalidToken()
    {
        $this->browser()->setDefaultHttpOptions(
            [
                'headers' => [
                    LandApiKeyAuthenticator::HEADER_ATTRIBUTE => 'invalid-token'
                ]
            ]
        )->get('/api/lands')->assertStatus(401);
    }

    public function testCannotCreateApiKeyThroughApi(): void
    {
        $context = $this->createLandContext();
        $apiKey = $this->createPersonApiKey($context->owner,
            ['permissions' => ['land_member:land_api_key:post']]);

        $landApiKey = $this->createLandApiKey($context->land,
            ['permissions' => ['land_member:land_api_key:post']]);

        $this->browser()->actingAs($apiKey)
            ->post('/api/land_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => ['land_member:land_task:post'],
                    'land' => $this->getIriFromResource($context->land)
                ]])->assertStatus(403);

        $this->browser()->actingAs($landApiKey)
            ->post('/api/land_api_keys',
                ['json' => [
                    'name' => 'Test API Key',
                    'permissions' => ['land_member:land_task:post'],
                    'land' => $this->getIriFromResource($context->land)
                ]])->assertStatus(403);
    }
}
