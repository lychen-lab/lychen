<?php

namespace App\Tests\Security;

use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandMemberSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landMembers[0]))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landMembers[0]))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot create a LandMember if they are not authenticated
        $this->browser()
            ->post('/api/land_members', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(405);

        // User cannot create a LandMember for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->post('/api/land_members', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(405);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addLandMember($context1);

        // User cannot patch a LandMember if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->landMembers[0]), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a LandMember for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->landMembers[0]), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandMember with a Land they are not a member of (land property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->landMembers[0]),
                ['json' => ['land' => $this->getIriFromResource($context2->land)]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->landMembers[0]))
            ->assertSuccessful();

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landMembers[0]))
            ->assertStatus(403);
    }

    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landMembers[0]))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landMembers[0]))
            ->assertStatus(405);
    }

    public function testDelete()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addLandMember($context1);

        // User cannot delete a Land if they are not authenticated
        $this->browser()
            ->delete($this->getIriFromResource($context1->landMembers[0]))
            ->assertStatus(401);

        // User cannot delete a LandMember for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->delete($this->getIriFromResource($context1->landMembers[0]))
            ->assertStatus(403);
    }

    public function testCollection()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addLandMember($context1);

        // User cannot list LandMember if they are not authenticated
        $this->browser()
            ->get('/api/land_members', ['query' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(401);

        // User cannot list LandMember without a Land query parameter
        $this->browser()->actingAs($context1->owner)
            ->get('/api/land_members', ['query' => ['land' => '']])
            ->assertStatus(400);

        // User cannot list LandMember for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_members', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);
    }

    public function testGetMe()
    {
        $context = $this->createLandContext();
        $context2 = $this->createLandContext();

        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_members/me', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertStatus(403);
    }

    public function testCantAddRolesFromAnotherLand()
    {
        $this->expectException(\Exception::class);

        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandRole($context1);
        $this->addOneLandRole($context2);
        $this->addLandMember($context1, [$context2->landRoles[0]], $context2->owner);
    }
}
