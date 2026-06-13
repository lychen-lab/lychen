<?php

namespace App\Tests\Functional\Land;

use App\Security\Voter\LandGreenhouseVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use DateTime;
use Zenstruck\Browser\Json;
use function Zenstruck\Foundry\faker;

class LandGreenhouseTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $context = $this->createLandContext();

        $name = faker()->name();
        $constructionDate = (new DateTime())->format('c');

        // Owner
        $this->browser()->actingAs($context->owner)
            ->post('/api/land_greenhouses',
                ['json' => [
                    'name' => $name,
                    'constructionDate' => $constructionDate,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('name', $name)
            ->assertJsonMatches('constructionDate', $constructionDate)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseVoter::POST]);
        $this->addLandMember($context, [$landRole]);

        $name = faker()->name();
        $constructionDate = (new DateTime())->format('c');

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->post('/api/land_greenhouses',
                ['json' => [
                    'name' => $name,
                    'constructionDate' => $constructionDate,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('name', $name)
            ->assertJsonMatches('constructionDate', $constructionDate)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $landGreenhouse = $context->landGreenhouses[0];

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landGreenhouse))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouse->getUlid()->toString())
            ->assertJsonMatches('name', $landGreenhouse->getName())
            ->assertJsonMatches('constructionDate', $landGreenhouse->getConstructionDate())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
                $json->assertThat('landGreenhouseSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landGreenhouseParameter', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landGreenhouse))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouse->getUlid()->toString())
            ->assertJsonMatches('name', $landGreenhouse->getName())
            ->assertJsonMatches('constructionDate', $landGreenhouse->getConstructionDate())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
                $json->assertThat('landGreenhouseSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landGreenhouseParameter', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $landGreenhouse = $context->landGreenhouses[0];

        $newName = faker()->name();
        $newConstructionDate = (new DateTime())->format('c');

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landGreenhouse),
                [
                    'json' => [
                        'name' => $newName,
                        'constructionDate' => $newConstructionDate
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landGreenhouse->getUlid()->toString())
            ->assertJsonMatches('name', $newName)
            ->assertJsonMatches('constructionDate', $newConstructionDate)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $newName = faker()->name();
        $newConstructionDate = (new DateTime())->format('c');

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landGreenhouse),
                [
                    'json' => [
                        'name' => $newName,
                        'constructionDate' => $newConstructionDate
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landGreenhouse->getUlid()->toString())
            ->assertJsonMatches('name', $newName)
            ->assertJsonMatches('constructionDate', $newConstructionDate)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);
        $this->addOneLandGreenhouse($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_greenhouses', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landGreenhouses))
            ->assertJsonMatches('member[0].ulid', $context->landGreenhouses[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].name', $context->landGreenhouses[0]->getName())
            ->assertJsonMatches('member[0].constructionDate', $context->landGreenhouses[0]->getConstructionDate())
            ->assertJsonMatches('member[1].ulid', $context->landGreenhouses[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].name', $context->landGreenhouses[1]->getName())
            ->assertJsonMatches('member[1].constructionDate', $context->landGreenhouses[1]->getConstructionDate());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseVoter::COLLECTION]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get('/api/land_greenhouses', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landGreenhouses))
            ->assertJsonMatches('member[0].ulid', $context->landGreenhouses[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].name', $context->landGreenhouses[0]->getName())
            ->assertJsonMatches('member[0].constructionDate', $context->landGreenhouses[0]->getConstructionDate())
            ->assertJsonMatches('member[1].ulid', $context->landGreenhouses[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].name', $context->landGreenhouses[1]->getName())
            ->assertJsonMatches('member[1].constructionDate', $context->landGreenhouses[1]->getConstructionDate());
    }

    public function testCollectionPagination()
    {
        $context = $this->createLandContext();
        array_map(fn() => $this->addOneLandGreenhouse($context), range(1, 25));

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_greenhouses',
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
        $this->addOneLandGreenhouse($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landGreenhouses[0]))
            ->assertStatus(204);

        // Member with permissions
        $this->addOneLandGreenhouse($context);
        $landRole = $this->createLandRole($context->land, [LandGreenhouseVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context->landGreenhouses[1]))
            ->assertStatus(204);
    }
}
