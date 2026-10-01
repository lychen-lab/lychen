<?php

namespace App\Tests\Functional\Land;

use App\Constant\GardeningLevel;
use App\Constant\LandInteractionMode;
use App\Constant\LandSharingCondition;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use App\Workflow\LandRequest\LandRequestWorkflowTransition;
use Lychen\UtilTiptap\Service\TipTapFaker;
use Zenstruck\Browser\Json;

class LandRequestTest extends AbstractApiTestCase
{
    public function testPost(): void
    {
        $person = $this->createPerson();

        $data = [
            'message' => TipTapFaker::randomContent(),
            'minimumSurfaceWanted' => 160,
            'gardeningLevel' => GardeningLevel::BEGINNER,
            'hasTools' => true,
            'title' => 'test',
            'preferredInteractionMode' => LandInteractionMode::TOGETHER_BUT_NOT_ALL_TIME,
            'supportsLocalFoodSecurity' => true,
            'sharingConditions' => [LandSharingCondition::VEGETABLE_SHARING, LandSharingCondition::GARDENING],
        ];

        $this->browser()->actingAs($person)
            ->post('/api/land_requests', ['json' => $data])
            ->assertStatus(201)
            ->assertJsonMatches('message', $data['message'])
            ->assertJsonMatches('minimumSurfaceWanted', $data['minimumSurfaceWanted'])
            ->assertJsonMatches('gardeningLevel', $data['gardeningLevel'])
            ->assertJsonMatches('hasTools', $data['hasTools'])
            ->assertJsonMatches('title', $data['title'])
            ->assertJsonMatches('preferredInteractionMode', $data['preferredInteractionMode'])
            ->assertJsonMatches('supportsLocalFoodSecurity', $data['supportsLocalFoodSecurity'])
            ->assertJsonMatches('sharingConditions', $data['sharingConditions'])
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn (Json $json) => $json->isNotNull());
                $json->assertThat('createdAt', fn (Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn (Json $json) => $json->isNull());
            });
    }

    public function testGet(): void
    {
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        $this->browser()->actingAs($person)
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(200)
            ->assertJsonMatches('message', $landRequest->getMessage())
            ->assertJsonMatches('minimumSurfaceWanted', $landRequest->getMinimumSurfaceWanted())
            ->assertJsonMatches('gardeningLevel', $landRequest->getGardeningLevel())
            ->assertJsonMatches('hasTools', $landRequest->getHasTools())
            ->assertJsonMatches('title', $landRequest->getTitle())
            ->assertJsonMatches('preferredInteractionMode', $landRequest->getPreferredInteractionMode())
            ->assertJsonMatches('supportsLocalFoodSecurity', $landRequest->getSupportsLocalFoodSecurity())
            ->assertJsonMatches('sharingConditions', $landRequest->getSharingConditions())
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn (Json $json) => $json->isNotNull());
            });

        $person2 = $this->createPerson();
        $landRequest = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::PUBLISHED]);
        $this->browser()->actingAs($person2)
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(200);
    }

    public function testCollection()
    {
        $person = $this->createPerson();

        $landRequest1 = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::ARCHIVED]);
        $landRequest2 = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::ARCHIVED]);
        $landRequest3 = $this->createLandRequest($person);
        $landRequest4 = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        $this->browser()->actingAs($person)
            ->get('/api/land_requests')
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 4)
            ->assertJsonMatches('member[0].ulid', $landRequest1->getUlid()->toString())
            ->assertJsonMatches('member[0].message', $landRequest1->getMessage())
            ->assertJsonMatches('member[0].minimumSurfaceWanted', $landRequest1->getMinimumSurfaceWanted())
            ->assertJsonMatches('member[0].gardeningLevel', $landRequest1->getGardeningLevel())
            ->assertJsonMatches('member[0].hasTools', $landRequest1->getHasTools())
            ->assertJsonMatches('member[0].title', $landRequest1->getTitle())
            ->assertJsonMatches('member[0].preferredInteractionMode',
                $landRequest1->getPreferredInteractionMode())
            ->assertJsonMatches('member[0].supportsLocalFoodSecurity', $landRequest1->getSupportsLocalFoodSecurity())
            ->assertJsonMatches('member[0].sharingConditions', $landRequest1->getSharingConditions());

        $this->browser()->actingAs($person)
            ->get('/api/land_requests', ['query' => ['state' => LandRequestWorkflowPlace::ARCHIVED]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2)
            ->assertJsonMatches('member[0].ulid', $landRequest1->getUlid()->toString())
            ->assertJsonMatches('member[1].ulid', $landRequest2->getUlid()->toString());

        $this->browser()->actingAs($person)
            ->get('/api/land_requests', ['query' => ['state' => LandRequestWorkflowPlace::DRAFT]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].ulid', $landRequest3->getUlid()->toString());

        $this->browser()->actingAs($person)
            ->get('/api/land_requests', ['query' => ['state' => LandRequestWorkflowPlace::PUBLISHED]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].ulid', $landRequest4->getUlid()->toString());
    }

    public function testCollectionPublic()
    {
        $person1 = $this->createPerson();
        $person2 = $this->createPerson();
        $person3 = $this->createPerson();

        $landRequest1 = $this->createLandRequest($person1);
        $landRequest2 = $this->createLandRequest($person1, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        $landRequest3 = $this->createLandRequest($person2);
        $landRequest4 = $this->createLandRequest($person2, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        $landRequest5 = $this->createLandRequest($person3);
        $landRequest6 = $this->createLandRequest($person3, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        $landRequest7 = $this->createLandRequest($person1, ['state' => LandRequestWorkflowPlace::ARCHIVED]);
        $landRequest8 = $this->createLandRequest($person1, ['state' => LandRequestWorkflowPlace::ARCHIVED]);
        $landRequest9 = $this->createLandRequest($person2, ['state' => LandRequestWorkflowPlace::ARCHIVED]);
        $landRequest10 = $this->createLandRequest($person3, ['state' => LandRequestWorkflowPlace::ARCHIVED]);

        $this->browser()->actingAs($person1)
            ->get('/api/land_requests/public')
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2) // All published without the one from the connected user
            ->assertJsonMatches('member[0].state', LandRequestWorkflowPlace::PUBLISHED)
            ->assertJsonMatches('member[1].state', LandRequestWorkflowPlace::PUBLISHED);
    }

    public function testPatch(): void
    {
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        $data = [
            'message' => TipTapFaker::randomContent(),
            'minimumSurfaceWanted' => 160,
            'gardeningLevel' => GardeningLevel::BEGINNER,
            'hasTools' => true,
            'title' => 'test',
            'preferredInteractionMode' => LandInteractionMode::TOGETHER_BUT_NOT_ALL_TIME,
            'supportsLocalFoodSecurity' => true,
            'sharingConditions' => [LandSharingCondition::VEGETABLE_SHARING, LandSharingCondition::GARDENING],
        ];

        $this->browser()->actingAs($person)
            ->patch($this->getIriFromResource($landRequest), ['json' => $data])
            ->assertStatus(200)
            ->assertJsonMatches('message', $data['message'])
            ->assertJsonMatches('minimumSurfaceWanted', $data['minimumSurfaceWanted'])
            ->assertJsonMatches('gardeningLevel', $data['gardeningLevel'])
            ->assertJsonMatches('hasTools', $data['hasTools'])
            ->assertJsonMatches('title', $data['title'])
            ->assertJsonMatches('preferredInteractionMode', $data['preferredInteractionMode'])
            ->assertJsonMatches('supportsLocalFoodSecurity', $data['supportsLocalFoodSecurity'])
            ->assertJsonMatches('sharingConditions', $data['sharingConditions'])
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn (Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn (Json $json) => $json->isNotNull());
            });
    }

    public function testDelete(): void
    {
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        $this->browser()->actingAs($person)
            ->delete($this->getIriFromResource($landRequest))
            ->assertStatus(204);
    }

    public function testPublish(): void
    {
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        $this->browser()->actingAs($person)
            ->patch($this->getIriFromResource($landRequest).'/'.LandRequestWorkflowTransition::PUBLISH)
            ->assertSuccessful()
            ->assertJsonMatches('state', LandRequestWorkflowPlace::PUBLISHED)
            ->use(function (Json $json) {
                $json->assertThat('expirationDate', fn (Json $json) => $json->isNotNull());
                $json->assertThat('publishedAt', fn (Json $json) => $json->isNotNull());
            });
    }

    public function testArchive(): void
    {
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        $this->browser()->actingAs($person)
            ->patch($this->getIriFromResource($landRequest).'/'.LandRequestWorkflowTransition::ARCHIVE)
            ->assertSuccessful()
            ->assertJsonMatches('state', LandRequestWorkflowPlace::ARCHIVED)
            ->use(function (Json $json) {
                $json->assertThat('archivedAt', fn (Json $json) => $json->isNotNull());
            });
    }

    public function testCannotCreateSecondDraft(): void
    {
        $person = $this->createPerson();
        $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::DRAFT]);

        $data = [
            'message' => TipTapFaker::randomContent(),
            'minimumSurfaceWanted' => 160,
            'gardeningLevel' => GardeningLevel::BEGINNER,
            'hasTools' => true,
            'title' => 'test',
            'preferredInteractionMode' => LandInteractionMode::TOGETHER_BUT_NOT_ALL_TIME,
            'supportsLocalFoodSecurity' => true,
            'sharingConditions' => [LandSharingCondition::VEGETABLE_SHARING],
        ];

        $this->browser()->actingAs($person)
            ->post('/api/land_requests', ['json' => $data])
            ->assertStatus(422)
            ->assertJsonMatches('violations[0].message', 'You can only have one request in draft state.');
    }

    public function testCannotCreateSecondPublished(): void
    {
        $person = $this->createPerson();
        $firstRequest = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        // Try to publish another request
        $secondRequest = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::DRAFT]);

        $this->browser()->actingAs($person)
            ->patch($this->getIriFromResource($secondRequest).'/'.LandRequestWorkflowTransition::PUBLISH)
            ->assertStatus(422)
            ->assertJsonMatches('violations[0].message', 'You can only have one request in published state.');
    }
}
