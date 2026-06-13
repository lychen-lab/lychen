<?php

namespace App\Tests\Security;

use App\Entity\Land;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandProposal\LandProposalWorkflowPlace;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;

class LandProposalSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land);

        $this->browser()
            ->put($this->getIriFromResource($landProposal))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($landProposal))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User not authenticated
        $this->browser()
            ->post('/api/land_proposals', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(401);

        // User isn't member of the land
        $this->browser()->actingAs($context2->owner)
            ->post('/api/land_proposals', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(403);

        // User haven't permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->post('/api/land_proposals', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $landProposal = $this->createLandProposal($context1->land);

        // User not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($landProposal), ['json' => []])
            ->assertStatus(401);

        // User isn't member of the land
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($landProposal), ['json' => []])
            ->assertStatus(403);

        // User cannot patch with a Land they are not a member of (land property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($landProposal),
                ['json' => ['land' => $this->getIriFromResource($context2->land)]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($landProposal))
            ->assertSuccessful()
            ->assertJsonMatches('land', $this->findIriBy(Land::class, ['id' => $context1->land->getId()]));

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($landProposal))
            ->assertStatus(403);

        // User cannot patch a LandProposal for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landProposal), ['json' => []])
            ->assertStatus(403);
    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $landProposal = $this->createLandProposal($context1->land);

        // User not authenticated
        $this->browser()
            ->get($this->getIriFromResource($landProposal))
            ->assertStatus(401);

        // User isn't member of the land
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($landProposal))
            ->assertStatus(403);

        // User cannot get a LandProposal for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landProposal))
            ->assertStatus(403);
    }

    public function testDelete()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $landProposal = $this->createLandProposal($context1->land);

        // User not authenticated
        $this->browser()
            ->delete($this->getIriFromResource($landProposal))
            ->assertStatus(401);

        // User isn't member of the land
        $this->browser()->actingAs($context2->owner)
            ->delete($this->getIriFromResource($landProposal))
            ->assertStatus(403);

        // User cannot delete a LandProposal for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($landProposal))
            ->assertStatus(403);
    }

    public function testCollection()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $landProposal = $this->createLandProposal($context1->land);

        // User not authenticated
        $this->browser()
            ->get('/api/land_proposals', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(401);

        // User cannot list LandProposal without a Land query parameter
        $this->browser()->actingAs($context1->owner)
            ->get('/api/land_proposals', ['query' => ['land' => '']])
            ->assertStatus(400);

        // User cannot list LandProposal for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_proposals', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);

        // User cannot list LandRole for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get('/api/land_proposals', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);
    }

    public function testPublish()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $landProposal = $this->createLandProposal($context1->land);

        // User not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($landProposal) . '/' . LandProposalWorkflowTransition::PUBLISH,
                ['json' => []])
            ->assertStatus(401);

        // User not the creator
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($landProposal) . '/' . LandProposalWorkflowTransition::PUBLISH,
                ['json' => []])
            ->assertStatus(403);
    }

    public function testArchive()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $landProposal = $this->createLandProposal($context1->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);

        // User not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($landProposal) . '/' . LandProposalWorkflowTransition::ARCHIVE,
                ['json' => []])
            ->assertStatus(401);

        // User not the creator
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($landProposal) . '/' . LandProposalWorkflowTransition::ARCHIVE,
                ['json' => []])
            ->assertStatus(403);
    }
}

