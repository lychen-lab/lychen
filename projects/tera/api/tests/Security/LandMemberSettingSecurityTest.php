<?php

namespace App\Tests\Security;

use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandMemberSettingSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $owner = $this->createPerson();

        $this->browser()
            ->post('/api/land_member_settings')
            ->assertStatus(404);

        // Does not exist
        $this->browser()->actingAs($owner)
            ->post('/api/land_member_settings')
            ->assertStatus(404);
    }

    public function testDelete()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);

        $this->browser()
            ->delete($this->getIriFromResource($context->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(405);
    }

    public function testCollection()
    {
        $owner = $this->createPerson();

        $this->browser()
            ->get('/api/land_member_settings')
            ->assertStatus(404);

        // Does not exist
        $this->browser()->actingAs($owner)
            ->get('/api/land_member_settings')
            ->assertStatus(404);
    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addLandMember($context1);

        // User cannot get a LandMemberSetting if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(401);

        // Owner cannot get a LandMemberSetting of members
        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(403);

        // User cannot get a LandMemberSetting for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(403);

        // User cannot get a LandMemberSetting for a LandMember for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[1]->getPerson())
            ->get($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()))
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $this->addLandMember($context1);

        $context2 = $this->createLandContext();
        $this->addLandMember($context2);

        // User cannot patch a LandMemberSetting if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()), ['json' => []])
            ->assertStatus(401);

        // Owner cannot patch a LandMemberSetting of members
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandMemberSetting for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandMemberSetting for a LandMember for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[1]->getPerson())
            ->patch($this->getIriFromResource($context1->landMembers[0]->getLandMemberSetting()), ['json' => []])
            ->assertStatus(403);
    }
}
