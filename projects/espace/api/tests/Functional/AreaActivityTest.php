<?php

namespace App\Tests\Functional;

use App\Factory\AreaActivityFactory;
use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class AreaActivityTest extends AbstractApiTestCase
{
    public function testGetCollectionReturnsActivitiesForAuthenticatedUser(): void
    {
        AreaActivityFactory::createMany(3);
        $person = PersonFactory::createOne();

        $this->browser()
            ->actingAs($person)
            ->get('/api/area_activities')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 3)
            ->assertJsonMatches('length(member)', 3)
            ->assertJsonMatches('member[0]."@type"', 'AreaActivityCollection');
    }

    public function testGetCollectionIsDeniedForAnonymousUser(): void
    {
        AreaActivityFactory::createMany(3);

        $this->browser()
            ->get('/api/area_activities')
            ->assertStatus(401);
    }
}
