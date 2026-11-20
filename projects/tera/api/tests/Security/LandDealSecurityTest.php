<?php

namespace App\Tests\Security;

use App\Tests\Utils\Abstract\AbstractApiTestCase;

class LandDealSecurityTest extends AbstractApiTestCase
{
    public function testPut(): void
    {
        $person = $this->createPerson();
        $context = $this->createLandContext();
        $landDeal = $this->createLandDeal($context->land, $person);

        $this->browser()
            ->put($this->getIriFromResource($landDeal))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($landDeal))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($person)
            ->put($this->getIriFromResource($landDeal))
            ->assertStatus(405);
    }
}
