<?php

namespace App\Tests\Functional\Land;

use App\Security\Voter\LandAreaSettingVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandAreaSettingTest extends AbstractApiTestCase
{
    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $landAreaSetting = $context->landAreas[0]->getLandAreaSetting();
        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landAreaSetting))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaSetting->getUlid()->toString())
            ->assertJsonMatches('rotationActivated', $landAreaSetting->isRotationActivated());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaSettingVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landAreaSetting))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaSetting->getUlid()->toString())
            ->assertJsonMatches('rotationActivated', $landAreaSetting->isRotationActivated());
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $landAreaSetting = $context->landAreas[0]->getLandAreaSetting();
        // Owner
        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landAreaSetting), ['json' => [
                'rotationActivated' => true,
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaSetting->getUlid()->toString())
            ->assertJsonMatches('rotationActivated', true);

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaSettingVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landAreaSetting), ['json' => [
                'rotationActivated' => false,
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaSetting->getUlid()->toString())
            ->assertJsonMatches('rotationActivated', false);
    }
}
