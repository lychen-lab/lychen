<?php

namespace App\Tests\Security;

use App\Factory\PersonFactory;
use Lychen\Test\AbstractApiTestCase;

class DefaultSecurityTest extends AbstractApiTestCase
{
    public function testDocumentationIsPublic(): void
    {
        $this->browser()->get('/api/docs')->assertStatus(200);
        $this->browser()->get('/api/docs.jsonopenapi')->assertStatus(200);
    }

    public function testAnonymousUserIsDeniedEverythingElse(): void
    {
        $this->browser()->get('/api')->assertStatus(401);
        $this->browser()->get('/api/contexts/Entrypoint')->assertStatus(401);
    }

    public function testAuthenticatedUserReachesTheApi(): void
    {
        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api')
            ->assertStatus(200);
    }
}
