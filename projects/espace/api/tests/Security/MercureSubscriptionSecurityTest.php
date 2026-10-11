<?php

namespace App\Tests\Security;

use Lychen\Test\AbstractApiTestCase;

class MercureSubscriptionSecurityTest extends AbstractApiTestCase
{
    public function testSubscriberTokenIsDeniedForAnonymousUser(): void
    {
        $this->browser()
            ->get('/api/mercure_subscription')
            ->assertStatus(401);
    }
}
