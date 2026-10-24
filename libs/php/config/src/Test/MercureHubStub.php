<?php

declare(strict_types=1);

namespace Lychen\Config\Test;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\ProtocolVersion;
use Symfony\Component\Mercure\Update;

/**
 * Stands in for the Mercure hub under test, so nothing goes over the network.
 *
 * It decorates the hub the bundle builds from the real configuration, so everything but
 * publishing (public URL, protocol version, cookie name) comes from that configuration.
 * The bundle's TraceableHub still wraps it and records every update for the assertions.
 * It has to be a stub rather than an unreachable URL: TraceableHub forwards to the hub
 * *before* recording, so a failing hub would lose the update first.
 */
final class MercureHubStub implements HubInterface
{
    public function __construct(private readonly HubInterface $hub)
    {
    }

    public function getPublicUrl(): string
    {
        return $this->hub->getPublicUrl();
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return $this->hub->getFactory();
    }

    public function publish(Update $update): string
    {
        return 'urn:uuid:'.bin2hex(random_bytes(16));
    }

    public function getProtocolVersion(): ProtocolVersion
    {
        return $this->hub->getProtocolVersion();
    }

    public function getCookieName(): string
    {
        return $this->hub->getCookieName();
    }
}
