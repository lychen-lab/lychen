<?php

namespace App\Tests\Security;

use App\Entity\LandApiKey;
use App\Entity\PersonApiKey;
use App\Security\Constant\LandMemberPermission;
use App\Security\Constant\PersonPermission;
use App\Security\Voter\LandVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();

        $this->browser()
            ->put($this->getIriFromResource($context->land))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->land))
            ->assertStatus(405);
    }

    public function testPost()
    {
        // User cannot create a Land if they are not authenticated
        $this->browser()
            ->post('/api/lands')
            ->assertStatus(401);

        // API Key
        $person = $this->createPerson();
        $personApiKey = $this->createPersonApiKey($person, ['permissions' => LandVoter::ALL_PERSON], [LandVoter::POST]);
        $this->browser()->actingAs($personApiKey)
            ->post('/api/lands', ['json' => ['name' => 'Test Land']])
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandArea($context1);

        // User cannot patch a Land if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->land), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->land), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context1->land), ['json' => []])
            ->assertStatus(403);

        // API Key
        $landApiKey = $this->createLandApiKey($context1->land, ['permissions' => LandMemberPermission::ALL],
            [LandVoter::PATCH]);
        $this->browser()->actingAs($landApiKey)
            ->patch($this->getIriFromResource($context1->land), ['json' => []])
            ->assertStatus(403);

        $landApiKey2 = $this->createLandApiKey($context2->land, ['permissions' => LandMemberPermission::ALL]);
        $this->browser()->actingAs($landApiKey2)
            ->patch($this->getIriFromResource($context1->land), ['json' => []])
            ->assertStatus(403);
    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot get a Land if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->land))
            ->assertStatus(401);

        // User cannot get a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->land))
            ->assertStatus(403);

        // User cannot get a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context1->land))
            ->assertStatus(403);

        // API Key
        $landApiKey = $this->createLandApiKey($context1->land, ['permissions' => LandMemberPermission::ALL],
            [LandVoter::GET]);
        $this->browser()->actingAs($landApiKey)
            ->get($this->getIriFromResource($context1->land))
            ->assertStatus(403);

        $landApiKey2 = $this->createLandApiKey($context2->land, ['permissions' => LandMemberPermission::ALL]);
        $this->browser()->actingAs($landApiKey2)
            ->get($this->getIriFromResource($context1->land))
            ->assertStatus(403);
    }

    public function testDelete()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot delete a Land if they are not authenticated
        $this->browser()
            ->delete($this->getIriFromResource($context1->land))
            ->assertStatus(401);

        // User cannot delete a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->delete($this->getIriFromResource($context1->land))
            ->assertStatus(403);

        // User cannot delete a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context1->land))
            ->assertStatus(403);

        // API Key
        $landApiKey = $this->createLandApiKey($context1->land, ['permissions' => LandMemberPermission::ALL],
            [LandVoter::DELETE]);
        $this->browser()->actingAs($landApiKey)
            ->delete($this->getIriFromResource($context1->land))
            ->assertStatus(403);

        $landApiKey2 = $this->createLandApiKey($context2->land, ['permissions' => LandMemberPermission::ALL]);
        $this->browser()->actingAs($landApiKey2)
            ->delete($this->getIriFromResource($context1->land))
            ->assertStatus(403);
    }

    public function testCollection()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot list Land if they are not authenticated
        $this->browser()
            ->get('/api/lands')
            ->assertStatus(401);

        // API Key
        $person = $this->createPerson();
        $personApiKey = $this->createPersonApiKey($person, ['permissions' => LandMemberPermission::ALL],
            [LandVoter::COLLECTION]);
        $this->browser()->actingAs($personApiKey)
            ->get('/api/lands')
            ->assertStatus(403);
    }

    public function testCollectionWithPersonApiKeyListsItsPersonsLands(): void
    {
        $otherContext = $this->createLandContext();
        $context = $this->createLandContext();

        // The key's id is the other owner's: it must stand for its own person, not for that id
        $this->giveNextRowTheIdOf(PersonApiKey::class, $otherContext->owner);
        $personApiKey = $this->createPersonApiKey($context->owner, ['permissions' => PersonPermission::ALL]);
        $this->assertSame($otherContext->owner->getId(), $personApiKey->getId());

        $this->assertSame([$this->getIriFromResource($context->land)], $this->listIris($context->owner, '/api/lands'));
        $this->assertSame([$this->getIriFromResource($context->land)], $this->listIris($personApiKey, '/api/lands'));
    }

    public function testCollectionWithLandApiKey(): void
    {
        $otherContext = $this->createLandContext();
        $land = $this->createLandContext()->land;

        // Should a land API key hold the land collection permission (written to the database, not
        // through the API), it stands for a land, not a person: not for the person whose id it has
        $this->giveNextRowTheIdOf(LandApiKey::class, $otherContext->owner);
        $landApiKey = $this->createLandApiKey($land, ['permissions' => [LandVoter::COLLECTION]]);
        $this->assertSame($otherContext->owner->getId(), $landApiKey->getId());

        $this->assertSame([], $this->listIris($landApiKey, '/api/lands'));

        // It cannot be given that permission through the API
        $this->browser()->actingAs($this->createLandApiKey($land, ['permissions' => LandMemberPermission::ALL]))
            ->get('/api/lands')
            ->assertStatus(403);
    }
}
