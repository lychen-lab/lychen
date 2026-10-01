<?php

namespace App\Tests\Functional\Land;

use App\Security\Voter\LandGreenhouseSettingVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandGreenhouseSettingTest extends AbstractApiTestCase
{
    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $landGreenhouseSetting = $context->landGreenhouses[0]->getLandGreenhouseSetting();
        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landGreenhouseSetting))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseSetting->getUlid()->toString());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseSettingVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landGreenhouseSetting))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseSetting->getUlid()->toString());
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandGreenhouse($context);

        $landGreenhouseSetting = $context->landGreenhouses[0]->getLandGreenhouseSetting();
        // Owner
        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landGreenhouseSetting), ['json' => [
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseSetting->getUlid()->toString());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandGreenhouseSettingVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landGreenhouseSetting), ['json' => [
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landGreenhouseSetting->getUlid()->toString());
    }
}
