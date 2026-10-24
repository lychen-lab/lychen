<?php

namespace App\Tests\Functional;

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
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

/**
 * Tests never publish to a real hub (see MercureHubStub), so this checks the token the API
 * would publish with against what the hub's `espace` tenant requires.
 */
class MercurePublisherTokenTest extends AbstractApiTestCase
{
    public function testUpdatesArePublishedWithAnAccessTokenTheHubAccepts(): void
    {
        $provider = self::getContainer()->get('mercure.hub.default.jwt.provider');
        self::assertInstanceOf(TokenProviderInterface::class, $provider);

        $jwt = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($_SERVER['MERCURE_JWT_SECRET']));
        $token = $jwt->parser()->parse($provider->getJwt());
        self::assertInstanceOf(UnencryptedToken::class, $token);
        self::assertTrue($jwt->validator()->validate(
            $token,
            new SignedWith($jwt->signer(), $jwt->verificationKey()),
            new LooseValidAt(Clock::get()),
            new IssuedBy('urn:lychen:espace-api'),
            // The tenant's resource identifier is the hub's public URL, whichever URL the API
            // reaches it through.
            new PermittedFor($_SERVER['MERCURE_PUBLIC_URL']),
        ));

        self::assertSame('at+jwt', $token->headers()->get('typ'));
        self::assertTrue($token->claims()->has('exp'), 'Publisher tokens must expire.');
        self::assertSame('urn:lychen:espace-api', $token->claims()->get('sub'));
        self::assertSame('urn:lychen:espace-api', $token->claims()->get('client_id'));
        self::assertSame([
            [
                'type' => 'https://mercure.rocks/authorization-detail',
                'actions' => ['publish'],
                'topics' => [['match' => '*']],
            ],
        ], $token->claims()->get('authorization_details'));
    }
}
