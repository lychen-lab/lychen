<?php

namespace App\Tests\Security;

use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class AreaActivitySecurityTest extends AbstractApiTestCase
{
    public function testGetCollectionIsDeniedForAnonymousUser(): void
    {
        $this->browser()
            ->get('/api/area_activities')
            ->assertStatus(401);
    }

    public function testGetCollectionIsAllowedForAuthenticatedUser(): void
    {
        $person = PersonFactory::createOne();

        $this->browser()
            ->actingAs($person)
            ->get('/api/area_activities')
            ->assertStatus(200);
    }
}
