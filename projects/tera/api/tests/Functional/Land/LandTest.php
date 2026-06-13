<?php

namespace App\Tests\Functional\Land;

use App\Security\Voter\LandVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Zenstruck\Browser\Json;
use function Zenstruck\Foundry\faker;

class LandTest extends AbstractApiTestCase
{
    #[DataProvider('landDataProvider')]
    public function testPost(int $surface,
                             int $altitude)
    {
        $person = $this->createPerson();

        $name = faker()->name();

        $this->browser()->actingAs($person)
            ->post('/api/lands', ['json' => [
                'name' => $name,
                'surface' => $surface,
                'altitude' => $altitude
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('name', $name)
            ->assertJsonMatches('surface', $surface)
            ->assertJsonMatches('altitude', $altitude)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });

        // API Key
        $person = $this->createPerson();
        $personApiKey = $this->createPersonApiKey($person, ['permissions' => [LandVoter::POST]]);
        $this->browser()->actingAs($personApiKey)
            ->post('/api/lands', ['json' => ['name' => faker()->name(),
                                             'surface' => $surface,
                                             'altitude' => $altitude]])
            ->assertStatus(201);
    }

    public function testGet()
    {
        $context = $this->createLandContext();

        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($context->land))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $context->land->getUlid()->toString())
            ->assertJsonMatches('name', $context->land->getName())
            ->assertJsonMatches('surface', $context->land->getSurface())
            ->assertJsonMatches('altitude', $context->land->getAltitude())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context->land))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $context->land->getUlid()->toString())
            ->assertJsonMatches('name', $context->land->getName())
            ->assertJsonMatches('surface', $context->land->getSurface())
            ->assertJsonMatches('altitude', $context->land->getAltitude())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
                $json->assertThat('landSetting', fn(Json $json) => $json->isNotNull());
            });

        // API Key
        $landApiKey = $this->createLandApiKey($context->land, ['permissions' => [LandVoter::GET]]);
        $this->browser()->actingAs($landApiKey)
            ->get($this->getIriFromResource($context->land))
            ->assertStatus(200);
    }

    public function testPatch()
    {
        $context = $this->createLandContext();

        $newName = faker()->name();
        $newSurface = faker()->numberBetween(10, 200);
        $newAltitude = faker()->numberBetween(-10, 1200);

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($context->land), [
                'json' => [
                    'name' => $newName,
                    'surface' => $newSurface,
                    'altitude' => $newAltitude,
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $context->land->getUlid()->toString())
            ->assertJsonMatches('name', $newName)
            ->assertJsonMatches('surface', $newSurface)
            ->assertJsonMatches('altitude', $newAltitude)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $newName = faker()->name();
        $newSurface = faker()->numberBetween(10, 200);
        $newAltitude = faker()->numberBetween(-10, 1200);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context->land), [
                'json' => [
                    'name' => $newName,
                    'surface' => $newSurface,
                    'altitude' => $newAltitude,
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $context->land->getUlid()->toString())
            ->assertJsonMatches('name', $newName)
            ->assertJsonMatches('surface', $newSurface)
            ->assertJsonMatches('altitude', $newAltitude)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });

        // API Key
        $landApiKey = $this->createLandApiKey($context->land, ['permissions' => [LandVoter::PATCH]]);
        $this->browser()->actingAs($landApiKey)
            ->patch($this->getIriFromResource($context->land), [
                'json' => [
                    'name' => $newName,
                    'surface' => $newSurface,
                    'altitude' => $newAltitude,
                ]
            ])
            ->assertStatus(200);
    }

    public function testPatchWithInvalidSurface()
    {
        $context = $this->createLandContext();

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($context->land), [
                'json' => [
                    'surface' => -1 // Invalid surface
                ]
            ])
            ->assertStatus(422);
    }

    public function testDelete()
    {
        $context = $this->createLandContext();

        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->land))
            ->assertStatus(204);

        // Member with permissions
        $context = $this->createLandContext();
        $landRole = $this->createLandRole($context->land, [LandVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context->land))
            ->assertStatus(204);

        // API Key
        $context = $this->createLandContext();
        $landApiKey = $this->createLandApiKey($context->land, ['permissions' => [LandVoter::DELETE]]);
        $this->browser()->actingAs($landApiKey)
            ->delete($this->getIriFromResource($context->land))
            ->assertStatus(204);
    }

    public function testCollection()
    {
        $context1 = $this->createLandContext();
        $this->createLand($context1->owner);

        $this->createLandContext(); // It is mandatory to ensure that lands are filtered based on the user

        $this->browser()->actingAs($context1->owner)
            ->get('/api/lands')
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2)
            ->assertJsonMatches('member[0].ulid', $context1->land->getUlid()->toString())
            ->assertJsonMatches('member[0].name', $context1->land->getName())
            ->assertJsonMatches('member[0].surface', $context1->land->getSurface())
            ->assertJsonMatches('member[0].altitude', $context1->land->getAltitude());

        // API Key
        $personApiKey = $this->createPersonApiKey($context1->owner, ['permissions' => [LandVoter::COLLECTION]]);
        $this->browser()->actingAs($personApiKey)
            ->get('/api/lands')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 2);
    }

    public function testCollectionPagination()
    {
        $owner = $this->createPerson();
        array_map(fn() => $this->createLand($owner), range(1, 25));

        $this->browser()->actingAs($owner)
            ->get('/api/lands', ['query' => ['itemsPerPage' => 10, 'page' => 2]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 25)
            ->use(function (Json $json) {
                $json->assertThat('member', fn(Json $json) => $json->hasCount(10));
            });
    }
}
