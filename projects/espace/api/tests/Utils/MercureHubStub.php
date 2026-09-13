<?php

namespace App\Tests\Utils;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\ProtocolVersion;
use Symfony\Component\Mercure\Update;

/**
 * Stands in for the Mercure hub under test, so nothing goes over the network.
 *
 * The bundle still wraps it in its TraceableHub, which records every update for the
 * assertions. It has to be a stub rather than an unreachable URL: TraceableHub forwards
 * to the hub *before* recording, so a failing hub would lose the update first.
 */
final class MercureHubStub implements HubInterface
{
    public function __construct(private readonly string $publicUrl)
    {
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return null;
    }

    public function publish(Update $update): string
    {
        return 'urn:uuid:'.bin2hex(random_bytes(16));
    }

    public function getProtocolVersion(): ProtocolVersion
    {
        return ProtocolVersion::Legacy;
    }

    public function getCookieName(): string
    {
        return ProtocolVersion::Legacy->getDefaultCookieName();
    }
}
