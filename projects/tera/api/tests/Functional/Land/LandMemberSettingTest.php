<?php

namespace App\Tests\Functional\Land;

use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandMemberSettingTest extends AbstractApiTestCase
{
    public function testGet()
    {
        $context = $this->createLandContext();
        $landRole = $this->createLandRole($context->land, []);
        $this->addLandMember($context, [$landRole]);

        $landMemberSetting = $context->landMembers[0]->getLandMemberSetting();

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landMemberSetting))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMemberSetting->getUlid()->toString())
            ->assertJsonMatches('emailNotificationActivated', $landMemberSetting->isEmailNotificationActivated());
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $landRole = $this->createLandRole($context->land, []);
        $this->addLandMember($context, [$landRole]);

        $landMemberSetting = $context->landMembers[0]->getLandMemberSetting();

        // Member without permissions
        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landMemberSetting), ['json' => [
                'emailNotificationActivated' => false,
            ]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMemberSetting->getUlid()->toString())
            ->assertJsonMatches('emailNotificationActivated', false);
    }
}
