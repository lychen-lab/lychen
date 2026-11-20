<?php

namespace App\Tests\Security;

use App\Constant\GardeningLevel;
use App\Constant\LandInteractionMode;
use App\Constant\LandSharingCondition;
use App\Entity\LandApiKey;
use App\Entity\PersonApiKey;
use App\Repository\LandMemberRepository;
use App\Security\Constant\LandMemberPermission;
use App\Security\Constant\PersonPermission;
use App\Security\Voter\LandRequestVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use App\Workflow\LandRequest\LandRequestWorkflowTransition;
use Lychen\UtilTiptap\Service\TipTapFaker;

class LandRequestSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        $this->browser()
            ->put($this->getIriFromResource($landRequest))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($person)
            ->put($this->getIriFromResource($landRequest))
            ->assertStatus(405);
    }

    public function testPost()
    {
        // User not authenticated
        $this->browser()
            ->post('/api/land_requests')
            ->assertStatus(401);

        // User cannot create for another user
        $person = $this->createPerson();
        $person2 = $this->createPerson();

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
            ->post('/api/land_requests',
                ['json' => array_merge($data, ['person' => $this->getIriFromResource($person2)])])
            ->assertStatus(201);

        $landRequestRepository = static::getContainer()->get(LandMemberRepository::class);

        $landRequest = $landRequestRepository->findOneBy(['person' => $person2]);

        $this->assertNull($landRequest);

        // API Key
        $person = $this->createPerson();
        $personApiKey = $this->createPersonApiKey($person,
            ['permissions' => PersonPermission::ALL],
            [LandRequestVoter::POST]);
        $this->browser()->actingAs($personApiKey)
            ->post('/api/land_requests', ['json' => $data])
            ->assertStatus(403);
    }

    public function testGet()
    {
        $person = $this->createPerson();
        $person2 = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        // User not authenticated
        $this->browser()
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(401);

        // User not the creator can't get on DRAFTED
        $this->browser()->actingAs($person2)
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(403);

        // User not the creator can't get on ARCHIVED
        $landRequest = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::ARCHIVED]);
        $this->browser()->actingAs($person2)
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(403);

        // API Key
        $personApiKey = $this->createPersonApiKey($person,
            ['permissions' => PersonPermission::ALL],
            [LandRequestVoter::GET]);
        $this->browser()->actingAs($personApiKey)
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(403);

        $personApiKey2 = $this->createPersonApiKey($person2, ['permissions' => PersonPermission::ALL]);
        $this->browser()->actingAs($personApiKey2)
            ->get($this->getIriFromResource($landRequest))
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $person = $this->createPerson();
        $person2 = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        // User not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($landRequest), ['json' => []])
            ->assertStatus(401);

        // User not the creator
        $this->browser()->actingAs($person2)
            ->patch($this->getIriFromResource($landRequest), ['json' => []])
            ->assertStatus(403);

        // API Key
        $personApiKey = $this->createPersonApiKey($person,
            ['permissions' => PersonPermission::ALL],
            [LandRequestVoter::PATCH]);
        $this->browser()->actingAs($personApiKey)
            ->patch($this->getIriFromResource($landRequest), ['json' => []])
            ->assertStatus(403);

        $personApiKey2 = $this->createPersonApiKey($person2, ['permissions' => PersonPermission::ALL]);
        $this->browser()->actingAs($personApiKey2)
            ->patch($this->getIriFromResource($landRequest), ['json' => []])
            ->assertStatus(403);
    }

    public function testDelete()
    {
        $person = $this->createPerson();
        $person2 = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        // User not authenticated
        $this->browser()
            ->delete($this->getIriFromResource($landRequest))
            ->assertStatus(401);

        // User not the creator
        $this->browser()->actingAs($person2)
            ->delete($this->getIriFromResource($landRequest))
            ->assertStatus(403);

        // API Key
        $personApiKey = $this->createPersonApiKey($person,
            ['permissions' => PersonPermission::ALL],
            [LandRequestVoter::DELETE]);
        $this->browser()->actingAs($personApiKey)
            ->delete($this->getIriFromResource($landRequest))
            ->assertStatus(403);

        $personApiKey2 = $this->createPersonApiKey($person2, ['permissions' => PersonPermission::ALL]);
        $this->browser()->actingAs($personApiKey2)
            ->delete($this->getIriFromResource($landRequest))
            ->assertStatus(403);
    }

    public function testCollection()
    {
        // User not authenticated
        $this->browser()
            ->get('/api/land_requests')
            ->assertStatus(401);

        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);
        $person2 = $this->createPerson();
        $landRequest2 = $this->createLandRequest($person2);
        $landRequest3 = $this->createLandRequest($person2);

        $this->browser()->actingAs($person)
            ->get('/api/land_requests')
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].ulid', $landRequest->getUlid()->toString());

        $this->browser()->actingAs($person2)
            ->get('/api/land_requests')
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2)
            ->assertJsonMatches('member[0].ulid', $landRequest2->getUlid()->toString())
            ->assertJsonMatches('member[1].ulid', $landRequest3->getUlid()->toString());

        // API Key
        $personApiKey = $this->createPersonApiKey($person,
            ['permissions' => PersonPermission::ALL],
            [LandRequestVoter::COLLECTION]);
        $this->browser()->actingAs($personApiKey)
            ->get('/api/land_requests')
            ->assertStatus(403);
    }

    public function testCollectionPublic()
    {
        // User not authenticated
        $this->browser()
            ->get('/api/land_requests/public')
            ->assertStatus(401);

        // API Key
        $person = $this->createPerson();
        $personApiKey = $this->createPersonApiKey($person,
            ['permissions' => PersonPermission::ALL],
            [LandRequestVoter::COLLECTION_PUBLIC]);
        $this->browser()->actingAs($personApiKey)
            ->get('/api/land_requests/public')
            ->assertStatus(403);
    }

    public function testCollectionWithPersonApiKeyListsItsPersonsOwn(): void
    {
        $otherPerson = $this->createPerson();
        $this->createLandRequest($otherPerson);
        $person = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        // The key's id is the other person's: it must stand for its own person, not for that id
        $this->giveNextRowTheIdOf(PersonApiKey::class, $otherPerson);
        $personApiKey = $this->createPersonApiKey($person, ['permissions' => PersonPermission::ALL]);
        $this->assertSame($otherPerson->getId(), $personApiKey->getId());

        $this->assertEqualsCanonicalizing(
            [$this->getIriFromResource($landRequest)],
            $this->listIris($personApiKey, '/api/land_requests'),
        );
    }

    public function testCollectionPublicWithPersonApiKeyHidesItsPersonsOwn(): void
    {
        $published = ['state' => LandRequestWorkflowPlace::PUBLISHED];
        $otherPerson = $this->createPerson();
        $otherLandRequest = $this->createLandRequest($otherPerson, $published);
        $person = $this->createPerson();
        $this->createLandRequest($person, $published);

        $this->giveNextRowTheIdOf(PersonApiKey::class, $otherPerson);
        $personApiKey = $this->createPersonApiKey($person, ['permissions' => PersonPermission::ALL]);
        $this->assertSame($otherPerson->getId(), $personApiKey->getId());

        // Same list as its person's: the others' published requests, not its person's own
        $this->assertEqualsCanonicalizing(
            [$this->getIriFromResource($otherLandRequest)],
            $this->listIris($person, '/api/land_requests/public'),
        );
        $this->assertEqualsCanonicalizing(
            [$this->getIriFromResource($otherLandRequest)],
            $this->listIris($personApiKey, '/api/land_requests/public'),
        );
    }

    public function testCollectionsWithLandApiKeyAreDenied(): void
    {
        // A land API key cannot be given a land request permission
        $landApiKey = $this->createLandApiKey($this->createLandContext()->land, ['permissions' => LandMemberPermission::ALL]);

        $this->browser()->actingAs($landApiKey)
            ->get('/api/land_requests')
            ->assertStatus(403);
        $this->browser()->actingAs($landApiKey)
            ->get('/api/land_requests/public')
            ->assertStatus(403);
    }

    public function testCollectionsWithLandApiKeyListNoPersonsRequests(): void
    {
        $published = ['state' => LandRequestWorkflowPlace::PUBLISHED];
        $person = $this->createPerson();
        $this->createLandRequest($person);
        $this->createLandRequest($person, $published);
        $this->createLandRequest($this->createPerson(), $published);

        // Should a land API key hold the permissions anyway (written to the database, not through the
        // API), it stands for a land, not a person: not for the person whose id it has
        $this->giveNextRowTheIdOf(LandApiKey::class, $person);
        $landApiKey = $this->createLandApiKey($this->createLandContext()->land, [
            'permissions' => [LandRequestVoter::COLLECTION, LandRequestVoter::COLLECTION_PUBLIC],
        ]);
        $this->assertSame($person->getId(), $landApiKey->getId());

        $this->assertSame([], $this->listIris($landApiKey, '/api/land_requests'));
        $this->assertSame([], $this->listIris($landApiKey, '/api/land_requests/public'));
    }

    public function testPublish()
    {
        $person = $this->createPerson();
        $person2 = $this->createPerson();
        $landRequest = $this->createLandRequest($person);

        // User not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($landRequest).'/'.LandRequestWorkflowTransition::PUBLISH,
                ['json' => []])
            ->assertStatus(401);

        // User not the creator
        $this->browser()->actingAs($person2)
            ->patch($this->getIriFromResource($landRequest).'/'.LandRequestWorkflowTransition::PUBLISH,
                ['json' => []])
            ->assertStatus(403);
    }

    public function testArchive()
    {
        $person = $this->createPerson();
        $person2 = $this->createPerson();
        $landRequest = $this->createLandRequest($person, ['state' => LandRequestWorkflowPlace::PUBLISHED]);

        // User not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($landRequest).'/'.LandRequestWorkflowTransition::ARCHIVE,
                ['json' => []])
            ->assertStatus(401);

        // User not the creator
        $this->browser()->actingAs($person2)
            ->patch($this->getIriFromResource($landRequest).'/'.LandRequestWorkflowTransition::ARCHIVE,
                ['json' => []])
            ->assertStatus(403);
    }
}
