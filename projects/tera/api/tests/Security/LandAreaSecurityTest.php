<?php

namespace App\Tests\Security;

use App\Entity\Land;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandAreaSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landAreas[0]))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landAreas[0]))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot create a LandArea if they are not authenticated
        $this->browser()
            ->post('/api/land_areas', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(401);

        // User cannot create a LandArea for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->post('/api/land_areas', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(403);

        // User cannot create a LandArea for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->post('/api/land_areas', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandArea($context1);

        // User cannot patch a LandArea if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->landAreas[0]), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a LandArea for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->landAreas[0]), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandArea with a Land they are not a member of (land property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->landAreas[0]),
                ['json' => ['land' => $this->getIriFromResource($context2->land)]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->landAreas[0]))
            ->assertSuccessful()
            ->assertJsonMatches('land', $this->findIriBy(Land::class, ['id' => $context1->land->getId()]));

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(403);

        // User cannot patch a LandArea for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context1->landAreas[0]), ['json' => []])
            ->assertStatus(403);

    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandArea($context1);

        // User cannot get a LandArea if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(401);

        // User cannot get a LandArea for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(403);

        // User cannot get a LandArea for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(403);
    }

    public function testDelete()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandArea($context1);

        // User cannot delete a Land if they are not authenticated
        $this->browser()
            ->delete($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(401);

        // User cannot delete a LandArea for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->delete($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(403);

        // User cannot delete a LandArea for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context1->landAreas[0]))
            ->assertStatus(403);
    }

    public function testCollection()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandArea($context1);

        // User cannot list LandArea if they are not authenticated
        $this->browser()
            ->get('/api/land_areas', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(401);

        // User cannot list LandArea without a Land query parameter
        $this->browser()->actingAs($context1->owner)
            ->get('/api/land_areas', ['query' => ['land' => '']])
            ->assertStatus(400);

        // User cannot list LandArea for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_areas', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);

        // User cannot list LandArea for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get('/api/land_areas', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);
    }
}
