<?php

namespace App\Tests\Functional\Land;

use App\Constant\LandAreaKind;
use App\Security\Voter\LandAreaVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Zenstruck\Browser\Json;
use function Zenstruck\Foundry\faker;

class LandAreaTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $context = $this->createLandContext();

        $name = faker()->name();
        $description = faker()->text();
        $kind = faker()->randomElement(LandAreaKind::ALL);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->post('/api/land_areas',
                ['json' => [
                    'name' => $name,
                    'description' => $description,
                    'kind' => $kind,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('name', $name)
            ->assertJsonMatches('description', $description)
            ->assertJsonMatches('kind', $kind)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaVoter::POST]);
        $this->addLandMember($context, [$landRole]);

        $name = faker()->name();
        $description = faker()->text();

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->post('/api/land_areas',
                ['json' => [
                    'name' => $name,
                    'description' => $description,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('name', $name)
            ->assertJsonMatches('description', $description)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $landArea = $context->landAreas[0];

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landArea))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landArea->getUlid()->toString())
            ->assertJsonMatches('name', $landArea->getName())
            ->assertJsonMatches('description', $landArea->getDescription())
            ->assertJsonMatches('state', $landArea->getState())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
                $json->assertThat('landAreaSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landAreaParameter', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landArea))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landArea->getUlid()->toString())
            ->assertJsonMatches('name', $landArea->getName())
            ->assertJsonMatches('description', $landArea->getDescription())
            ->assertJsonMatches('state', $landArea->getState())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
                $json->assertThat('landAreaSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landAreaParameter', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $landArea = $context->landAreas[0];

        $newName = faker()->name();
        $newDescription = faker()->paragraph();

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landArea),
                [
                    'json' => [
                        'name' => $newName,
                        'description' => $newDescription
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landArea->getUlid()->toString())
            ->assertJsonMatches('name', $newName)
            ->assertJsonMatches('description', $newDescription)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landArea),
                [
                    'json' => [
                        'name' => $newName,
                        'description' => $newDescription
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landArea->getUlid()->toString())
            ->assertJsonMatches('name', $newName)
            ->assertJsonMatches('description', $newDescription)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);
        $this->addOneLandArea($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_areas', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landAreas))
            ->assertJsonMatches('member[0].ulid', $context->landAreas[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].name', $context->landAreas[0]->getName())
            ->assertJsonMatches('member[0].description', $context->landAreas[0]->getDescription())
            ->assertJsonMatches('member[0].state', $context->landAreas[0]->getState())
            ->assertJsonMatches('member[1].ulid', $context->landAreas[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].name', $context->landAreas[1]->getName())
            ->assertJsonMatches('member[1].description', $context->landAreas[1]->getDescription())
            ->assertJsonMatches('member[1].state', $context->landAreas[1]->getState());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaVoter::COLLECTION]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get('/api/land_areas', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landAreas))
            ->assertJsonMatches('member[0].ulid', $context->landAreas[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].name', $context->landAreas[0]->getName())
            ->assertJsonMatches('member[0].description', $context->landAreas[0]->getDescription())
            ->assertJsonMatches('member[0].state', $context->landAreas[0]->getState())
            ->assertJsonMatches('member[1].ulid', $context->landAreas[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].name', $context->landAreas[1]->getName())
            ->assertJsonMatches('member[1].description', $context->landAreas[1]->getDescription())
            ->assertJsonMatches('member[1].state', $context->landAreas[1]->getState());
    }

    public function testCollectionPagination()
    {
        $context = $this->createLandContext();
        array_map(fn() => $this->addOneLandArea($context), range(1, 25));

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_areas',
                ['query' => ['land' => $context->land->getUlid()->toString(), 'itemsPerPage' => 10, 'page' => 2]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 25)
            ->use(function (Json $json) {
                $json->assertThat('member', fn(Json $json) => $json->hasCount(10));
            });
    }

    public function testDelete()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landAreas[0]))
            ->assertStatus(204);

        // Member with permissions
        $this->addOneLandArea($context);
        $landRole = $this->createLandRole($context->land, [LandAreaVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context->landAreas[1]))
            ->assertStatus(204);
    }
}
