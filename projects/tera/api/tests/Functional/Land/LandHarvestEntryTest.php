<?php

namespace App\Tests\Functional\Land;

use App\Constant\HarvestQuality;
use App\Security\Voter\LandHarvestEntryVoter;
use App\Service\PlantVerifier;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Tests\Utils\Mock\PlantVerifierMockTrait;
use Lychen\UtilTiptap\Service\TipTapFaker;
use Symfony\Component\Uid\Ulid;
use Zenstruck\Browser\Json;
use function Zenstruck\Foundry\faker;

class LandHarvestEntryTest extends AbstractApiTestCase
{
    use PlantVerifierMockTrait;

    public function testPost()
    {
        $context = $this->createLandContext();

        $weight = faker()->numberBetween(0, 10000);
        $notes = TipTapFaker::paragraphs();
        $harvestedAt = faker()->dateTimeThisMonth()->format('c');
        $quality = faker()->randomElement(HarvestQuality::ALL);
        $plantId = new Ulid()->toString();

        // Owner
        $this->browser()
            ->addMock(PlantVerifier::class, $this->preventAssertPlantExistsException($this->getMockedPlantVerifier()))
            ->actingAs($context->owner)
            ->post('/api/land_harvest_entries',
                ['json' => [
                    'weight' => $weight,
                    'notes' => $notes,
                    'harvestedAt' => $harvestedAt,
                    'quality' => $quality,
                    'land' => $this->getIriFromResource($context->land),
                    'plantId' => $plantId
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('weight', $weight)
            ->assertJsonMatches('notes', $notes)
            ->assertJsonMatches('quality', $quality)
            ->assertJsonMatches('harvestedAt', $harvestedAt)
            ->assertJsonMatches('plantId', $plantId)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandHarvestEntryVoter::POST]);
        $this->addLandMember($context, [$landRole]);

        $weight = faker()->numberBetween(0, 10000);
        $notes = TipTapFaker::paragraphs();
        $quality = faker()->randomElement(HarvestQuality::ALL);
        $plantId = new Ulid()->toString();

        $this->browser()
            ->addMock(PlantVerifier::class, $this->preventAssertPlantExistsException($this->getMockedPlantVerifier()))
            ->actingAs($context->landMembers[0]->getPerson())
            ->post('/api/land_harvest_entries',
                ['json' => [
                    'weight' => $weight,
                    'notes' => $notes,
                    'quality' => $quality,
                    'land' => $this->getIriFromResource($context->land),
                    'plantId' => $plantId
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('weight', $weight)
            ->assertJsonMatches('notes', $notes)
            ->assertJsonMatches('quality', $quality)
            ->assertJsonMatches('plantId', $plantId)
            ->use(function (Json $json) {
                $json->assertThat('harvestedAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandHarvestEntry($context);

        $landHarvestEntry = $context->landHarvestEntries[0];

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landHarvestEntry))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landHarvestEntry->getUlid()->toString())
            ->assertJsonMatches('weight', $landHarvestEntry->getWeight())
            ->assertJsonMatches('notes', $landHarvestEntry->getNotes())
            ->assertJsonMatches('quality', $landHarvestEntry->getQuality())
            ->assertJsonMatches('plantId', $landHarvestEntry->getPlantId()->toString())
            ->use(function (Json $json) {
                $json->assertThat('harvestedAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandHarvestEntryVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landHarvestEntry))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landHarvestEntry->getUlid()->toString())
            ->assertJsonMatches('weight', $landHarvestEntry->getWeight())
            ->assertJsonMatches('notes', $landHarvestEntry->getNotes())
            ->assertJsonMatches('quality', $landHarvestEntry->getQuality())
            ->assertJsonMatches('plantId', $landHarvestEntry->getPlantId()->toString())
            ->use(function (Json $json) {
                $json->assertThat('harvestedAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandHarvestEntry($context);

        $landHarvestEntry = $context->landHarvestEntries[0];

        $newWeight = faker()->numberBetween(0, 10000);
        $newNotes = TipTapFaker::paragraphs();
        $newQuality = faker()->randomElement(HarvestQuality::ALL);
        $newPlantId = new Ulid()->toString();

        $this->browser()
            ->addMock(PlantVerifier::class, $this->preventAssertPlantExistsException($this->getMockedPlantVerifier()))
            ->actingAs($context->owner)
            ->patch($this->getIriFromResource($landHarvestEntry),
                [
                    'json' => [
                        'weight' => $newWeight,
                        'notes' => $newNotes,
                        'quality' => $newQuality,
                        'plantId' => $newPlantId
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landHarvestEntry->getUlid()->toString())
            ->assertJsonMatches('weight', $newWeight)
            ->assertJsonMatches('notes', $newNotes)
            ->assertJsonMatches('quality', $newQuality)
            ->assertJsonMatches('plantId', $newPlantId)
            ->use(function (Json $json) {
                $json->assertThat('harvestedAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });

        $context = $this->createLandContext();
        $this->addOneLandHarvestEntry($context);

        $landHarvestEntry = $context->landHarvestEntries[0];
        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandHarvestEntryVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $newWeight = faker()->numberBetween(0, 10000);
        $newNotes = TipTapFaker::paragraphs();
        $newQuality = faker()->randomElement(HarvestQuality::ALL);
        $newPlantId = new Ulid()->toString();


        $this->browser()
            ->addMock(PlantVerifier::class, $this->preventAssertPlantExistsException($this->getMockedPlantVerifier()))
            ->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landHarvestEntry),
                [
                    'json' => [
                        'weight' => $newWeight,
                        'notes' => $newNotes,
                        'quality' => $newQuality,
                        'plantId' => $newPlantId
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landHarvestEntry->getUlid()->toString())
            ->assertJsonMatches('weight', $newWeight)
            ->assertJsonMatches('notes', $newNotes)
            ->assertJsonMatches('quality', $newQuality)
            ->assertJsonMatches('plantId', $newPlantId)
            ->use(function (Json $json) {
                $json->assertThat('harvestedAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $this->addOneLandHarvestEntry($context);
        $this->addOneLandHarvestEntry($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_harvest_entries', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landHarvestEntries))
            ->assertJsonMatches('member[0].ulid', $context->landHarvestEntries[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].weight', $context->landHarvestEntries[0]->getWeight())
            ->assertJsonMatches('member[0].quality', $context->landHarvestEntries[0]->getQuality())
            ->assertJsonMatches('member[0].plantId', $context->landHarvestEntries[0]->getPlantId()->toString())
            //->assertJsonMatches('member[0].notes', $context->landHarvestEntries[0]->getNotes())
            ->use(function (Json $json) {
                $json->assertThat('member[0].harvestedAt', fn(Json $json) => $json->isNotNull());
            })
            ->assertJsonMatches('member[1].ulid', $context->landHarvestEntries[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].weight', $context->landHarvestEntries[1]->getWeight())
            ->assertJsonMatches('member[1].quality', $context->landHarvestEntries[1]->getQuality())
            ->assertJsonMatches('member[1].plantId', $context->landHarvestEntries[1]->getPlantId()->toString())
            //->assertJsonMatches('member[1].notes', $context->landHarvestEntries[1]->getNotes())
            ->use(function (Json $json) {
                $json->assertThat('member[1].harvestedAt', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandHarvestEntryVoter::COLLECTION]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get('/api/land_harvest_entries', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landHarvestEntries))
            ->assertJsonMatches('member[0].ulid', $context->landHarvestEntries[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].weight', $context->landHarvestEntries[0]->getWeight())
            ->assertJsonMatches('member[0].quality', $context->landHarvestEntries[0]->getQuality())
            ->assertJsonMatches('member[0].plantId', $context->landHarvestEntries[0]->getPlantId()->toString())
            //->assertJsonMatches('member[0].notes', $context->landHarvestEntries[0]->getNotes())
            ->use(function (Json $json) {
                $json->assertThat('member[0].harvestedAt', fn(Json $json) => $json->isNotNull());
            })
            ->assertJsonMatches('member[1].ulid', $context->landHarvestEntries[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].weight', $context->landHarvestEntries[1]->getWeight())
            ->assertJsonMatches('member[1].quality', $context->landHarvestEntries[1]->getQuality())
            ->assertJsonMatches('member[1].plantId', $context->landHarvestEntries[1]->getPlantId()->toString())
            //->assertJsonMatches('member[1].notes', $context->landHarvestEntries[1]->getNotes())
            ->use(function (Json $json) {
                $json->assertThat('member[1].harvestedAt', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testCollectionPagination()
    {
        $context = $this->createLandContext();
        array_map(fn() => $this->addOneLandHarvestEntry($context), range(1, 25));

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_harvest_entries',
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
        $this->addOneLandHarvestEntry($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landHarvestEntries[0]))
            ->assertStatus(204);

        // Member with permissions
        $this->addOneLandHarvestEntry($context);
        $landRole = $this->createLandRole($context->land, [LandHarvestEntryVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context->landHarvestEntries[1]))
            ->assertStatus(204);
    }
}
