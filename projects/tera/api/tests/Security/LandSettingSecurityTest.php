<?php

namespace App\Tests\Security;

use App\Entity\Land;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandSettingSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();

        $this->browser()
            ->put($this->getIriFromResource($context->land->getLandSetting()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->land->getLandSetting()))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $owner = $this->createPerson();

        $this->browser()
            ->post('/api/land_settings')
            ->assertStatus(404);

        // Does not exist
        $this->browser()->actingAs($owner)
            ->post('/api/land_settings')
            ->assertStatus(404);
    }

    public function testDelete()
    {
        $context = $this->createLandContext();

        $this->browser()
            ->delete($this->getIriFromResource($context->land->getLandSetting()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->land->getLandSetting()))
            ->assertStatus(405);
    }

    public function testCollection()
    {
        $owner = $this->createPerson();

        $this->browser()
            ->get('/api/land_settings')
            ->assertStatus(404);

        // Does not exist
        $this->browser()->actingAs($owner)
            ->get('/api/land_settings')
            ->assertStatus(404);
    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot get a LandSetting if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->land->getLandSetting()))
            ->assertStatus(401);

        // User cannot get a LandSetting for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->land->getLandSetting()))
            ->assertStatus(403);

        // User cannot get a LandSetting for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context1->land->getLandSetting()))
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot patch a LandSetting if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->land->getLandSetting()), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a LandSetting for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->land->getLandSetting()), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandSetting with a Land they are not a member of (land property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->land->getLandSetting()), ['json' => ['land' => $this->getIriFromResource($context2->land)]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->land->getLandSetting()))
            ->assertSuccessful()
            ->assertJsonMatches('land', $this->findIriBy(Land::class, ['id' => $context1->land->getId()]));

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->land->getLandSetting()))
            ->assertStatus(403);

        // User cannot patch a LandSetting for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context1->land->getLandSetting()), ['json' => []])
            ->assertStatus(403);
    }
}
