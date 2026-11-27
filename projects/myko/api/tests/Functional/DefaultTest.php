<?php

namespace App\Tests\Functional;

use App\Factory\PersonFactory;
use Lychen\Test\AbstractApiTestCase;

class DefaultTest extends AbstractApiTestCase
{
    public function testDocumentationIsServed(): void
    {
        $this->browser()
            ->get('/api/docs.jsonopenapi')
            ->assertStatus(200)
            ->assertJsonMatches('info.title', 'Myko');
    }

    public function testEntrypointAnswersAnAuthenticatedUser(): void
    {
        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api')
            ->assertStatus(200);
    }
}
