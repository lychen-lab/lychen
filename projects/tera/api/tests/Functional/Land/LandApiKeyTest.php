<?php

namespace App\Tests\Functional\Land;

use App\Entity\LandApiKey;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Zenstruck\Browser\Json;

class LandApiKeyTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $context = $this->createLandContext();

        $this->browser()->actingAs($context->owner)
            ->post('/api/land_api_keys', ['json' => [
                'name' => 'Test API Key',
                'permissions' => ['land_member:land_task:post', 'land_member:land_task:collection'],
                'land' => $this->getIriFromResource($context->land),
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('name', 'Test API Key')
            ->assertJsonMatches('permissions', ['land_member:land_task:post', 'land_member:land_task:collection'])
            ->assertJsonMatches('lastUsedDate', null)
            ->assertJsonMatches('expirationDate', null)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn (Json $json) => $json->isNotNull());
                $json->assertThat('token', fn (Json $json) => $json->isNotNull());
                $json->assertThat('token', fn (Json $json) => str_starts_with($json->decoded(), LandApiKey::PREFIX));
            });
    }
}
