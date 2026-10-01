<?php

namespace App\Tests\Security;

use App\Entity\LandArea;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandAreaParameterSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $owner = $this->createPerson();

        $this->browser()
            ->post('/api/land_area_parameters')
            ->assertStatus(404);

        // Does not exist
        $this->browser()->actingAs($owner)
            ->post('/api/land_area_parameters')
            ->assertStatus(404);
    }

    public function testDelete()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $this->browser()
            ->delete($this->getIriFromResource($context->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(405);
    }

    public function testCollection()
    {
        $owner = $this->createPerson();

        $this->browser()
            ->get('/api/land_area_parameters')
            ->assertStatus(404);

        // Does not exist
        $this->browser()->actingAs($owner)
            ->get('/api/land_area_parameters')
            ->assertStatus(404);
    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandArea($context1);

        // User cannot get a LandAreaParameter if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(401);

        // User cannot get a LandAreaParameter for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(403);

        // User cannot get a LandAreaParameter for a LandArea for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $this->addOneLandArea($context1);

        $context2 = $this->createLandContext();
        $this->addOneLandArea($context2);

        // User cannot patch a LandAreaParameter if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a LandAreaParameter for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandAreaParameter with a LandArea they are not a member of (landArea property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()), ['json' => ['landArea' => $this->getIriFromResource($context2->landAreas[0])]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()))
            ->assertSuccessful()
            ->assertJsonMatches('landArea', $this->findIriBy(LandArea::class, ['id' => $context1->landAreas[0]->getId()]));

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()))
            ->assertStatus(403);

        // User cannot patch a LandAreaParameter for a LandArea for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context1->landAreas[0]->getLandAreaParameter()), ['json' => []])
            ->assertStatus(403);
    }
}
