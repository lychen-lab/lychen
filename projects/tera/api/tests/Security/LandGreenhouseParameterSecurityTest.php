<?php

namespace App\Tests\Security;

use App\Entity\LandGreenhouse;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandGreenhouseParameterSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landGreenhouses[0]->getLandGreenhouseParameter()))
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
        $this->addOneLandGreenhouse($context);

        $this->browser()
            ->delete($this->getIriFromResource($context->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landGreenhouses[0]->getLandGreenhouseParameter()))
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
        $this->addOneLandGreenhouse($context1);

        // User cannot get a LandGreenhouseParameter if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertStatus(401);

        // User cannot get a LandGreenhouseParameter for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertStatus(403);

        // User cannot get a LandGreenhouseParameter for a LandGreenhouse for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $this->addOneLandGreenhouse($context1);

        $context2 = $this->createLandContext();
        $this->addOneLandGreenhouse($context2);

        // User cannot patch a LandGreenhouseParameter if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a LandGreenhouseParameter for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandGreenhouseParameter with a LandGreenhouse they are not a member of (landGreenhouse property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()), ['json' => ['landGreenhouse' => $this->getIriFromResource($context2->landGreenhouses[0])]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertSuccessful()
            ->assertJsonMatches('landGreenhouse', $this->findIriBy(LandGreenhouse::class, ['id' => $context1->landGreenhouses[0]->getId()]));

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()))
            ->assertStatus(403);

        // User cannot patch a LandGreenhouseParameter for a LandGreenhouse for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context1->landGreenhouses[0]->getLandGreenhouseParameter()), ['json' => []])
            ->assertStatus(403);
    }
}
