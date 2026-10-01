<?php

declare(strict_types=1);

namespace Lychen\ConfigBundle\DependencyInjection\Compiler;

use Lychen\ConfigBundle\Test\MercureHubStub;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Tests never reach a real Mercure hub, see MercureHubStub.
 *
 * A pass rather than a service of the bundle: MercureBundle defines the hub too, and
 * whichever extension loads last would win. Replacing the definition here, before the
 * optimization passes, still lets the bundle's TraceableHub decorate the stub.
 */
final class StubMercureHubPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ('test' !== $container->getParameter('kernel.environment') || !$container->hasDefinition('mercure.hub.default')) {
            return;
        }

        $container->setDefinition(
            'mercure.hub.default',
            (new Definition(MercureHubStub::class, ['%env(MERCURE_PUBLIC_URL)%']))->setPublic(true),
        );
    }
}
