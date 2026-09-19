<?php

namespace App\Tests\Functional;

use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Symfony\Component\Clock\Clock;

class MercureSubscriptionTest extends AbstractApiTestCase
{
    public function testSignedInUserGetsATokenToSubscribeToAreaProposals(): void
    {
        $subscription = $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api/mercure_subscription')
            ->assertStatus(200)
            ->json()->decoded();

        self::assertSame($_SERVER['MERCURE_PUBLIC_URL'], $subscription['hubUrl']);

        // Signed with the *subscriber* key: the one the hub checks subscribers against, and
        // one that cannot publish.
        $jwt = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($_SERVER['MERCURE_SUBSCRIBER_JWT_SECRET']));
        $token = $jwt->parser()->parse($subscription['token']);
        self::assertInstanceOf(UnencryptedToken::class, $token);
        self::assertTrue($jwt->validator()->validate(
            $token,
            new SignedWith($jwt->signer(), $jwt->verificationKey()),
            new LooseValidAt(Clock::get()),
        ));

        self::assertTrue($token->claims()->has('exp'), 'Subscriber tokens must expire.');
        self::assertSame(['subscribe' => ['/api/area_proposals/{uuid}']], $token->claims()->get('mercure'));
    }
}
