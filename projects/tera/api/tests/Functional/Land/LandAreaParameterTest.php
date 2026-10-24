<?php

namespace App\Tests\Functional\Land;

use App\Security\Voter\LandAreaParameterVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandAreaParameterTest extends AbstractApiTestCase
{
    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $landAreaParameter = $context->landAreas[0]->getLandAreaParameter();
        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landAreaParameter))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaParameter->getUlid()->toString())
            ->assertJsonMatches('aboveGround', $landAreaParameter->isAboveGround())
            ->assertJsonMatches('width', $landAreaParameter->getWidth())
            ->assertJsonMatches('length', $landAreaParameter->getLength());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaParameterVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landAreaParameter))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaParameter->getUlid()->toString())
            ->assertJsonMatches('aboveGround', $landAreaParameter->isAboveGround())
            ->assertJsonMatches('width', $landAreaParameter->getWidth())
            ->assertJsonMatches('length', $landAreaParameter->getLength());
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandArea($context);

        $landAreaParameter = $context->landAreas[0]->getLandAreaParameter();
        $newWidthAndLength = 20;
        // Owner
        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landAreaParameter), ['json' => [
                'aboveGround' => true,
                'width' => $newWidthAndLength,
                'length' => $newWidthAndLength,
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaParameter->getUlid()->toString())
            ->assertJsonMatches('aboveGround', true)
            ->assertJsonMatches('width', $newWidthAndLength)
            ->assertJsonMatches('length', $newWidthAndLength);

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandAreaParameterVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);
        $newWidthAndLength = 40;
        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landAreaParameter), ['json' => [
                'aboveGround' => false,
                'width' => $newWidthAndLength,
                'length' => $newWidthAndLength,
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landAreaParameter->getUlid()->toString())
            ->assertJsonMatches('aboveGround', false)
            ->assertJsonMatches('width', $newWidthAndLength)
            ->assertJsonMatches('length', $newWidthAndLength);
    }
}
