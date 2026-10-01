<?php

namespace App\Tests\Functional\Land;

use App\Security\Voter\LandGreenhouseParameterVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandGreenhouseParameterTest extends AbstractApiTestCase
{
    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $landGreenhouseParameter = $context->landGreenhouses[0]->getLandGreenhouseParameter();
        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landGreenhouseParameter))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseParameter->getUlid()->toString());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseParameterVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landGreenhouseParameter))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseParameter->getUlid()->toString());
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $landGreenhouseParameter = $context->landGreenhouses[0]->getLandGreenhouseParameter();
        $newWidthAndLength = 20;
        // Owner
        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landGreenhouseParameter), ['json' => [
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseParameter->getUlid()->toString());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseParameterVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);
        $newWidthAndLength = 40;
        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landGreenhouseParameter), ['json' => [
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseParameter->getUlid()->toString());
    }
}
