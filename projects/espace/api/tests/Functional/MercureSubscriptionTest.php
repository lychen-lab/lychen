<?php

namespace App\Tests\Functional;

use App\Factory\PersonFactory;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lychen\Test\AbstractApiTestCase;
use Symfony\Component\Clock\Clock;

class MercureSubscriptionTest extends AbstractApiTestCase
{
    public function testSignedInUserGetsATokenToSubscribeToAreaProposals(): void
    {
        $person = PersonFactory::createOne();

        $subscription = $this->browser()
            ->actingAs($person)
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
            // What the hub's `espace` tenant checks on top of the signature: the issuer it
            // trusts, and its own resource identifier, which is its public URL.
            new IssuedBy('urn:lychen:espace-api'),
            new PermittedFor($_SERVER['MERCURE_PUBLIC_URL']),
        ));

        // An RFC 9068 access token, as the Mercure 1.0 protocol requires.
        self::assertSame('at+jwt', $token->headers()->get('typ'));
        self::assertTrue($token->claims()->has('exp'), 'Subscriber tokens must expire.');
        self::assertSame($person->getUserIdentifier(), $token->claims()->get('sub'));
        self::assertSame('urn:lychen:espace-api', $token->claims()->get('client_id'));
        self::assertSame([
            [
                'type' => 'https://mercure.rocks/authorization-detail',
                'actions' => ['subscribe'],
                'topics' => [
                    ['match' => '/api/area_proposals/:uuid', 'match_type' => 'urlpattern'],
                ],
            ],
        ], $token->claims()->get('authorization_details'));
    }
}
