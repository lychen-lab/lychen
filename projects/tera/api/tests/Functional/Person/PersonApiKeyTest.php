<?php

namespace App\Tests\Functional\Person;

use App\Entity\PersonApiKey;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Zenstruck\Browser\Json;

class PersonApiKeyTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $person = $this->createPerson();

        $this->browser()->actingAs($person)
            ->post('/api/person_api_keys', ['json' => [
                'name' => 'Test API Key',
                'permissions' => ['person:land:post', 'person:land:collection'],
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('name', 'Test API Key')
            ->assertJsonMatches('permissions', ['person:land:post', 'person:land:collection'])
            ->assertJsonMatches('lastUsedDate', null)
            ->assertJsonMatches('expirationDate', null)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn (Json $json) => $json->isNotNull());
                $json->assertThat('token', fn (Json $json) => $json->isNotNull());
                $json->assertThat('token', fn (Json $json) => str_starts_with($json->decoded(), PersonApiKey::PREFIX));
            });
    }
}
