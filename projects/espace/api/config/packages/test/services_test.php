<?php

declare(strict_types=1);

use App\Tests\Utils\MercureHubStub;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->alias('security', Security::class);

    // Tests never reach a real hub, see MercureHubStub. It sits between the configured hub
    // and the bundle's TraceableHub decorator: a higher priority is applied first.
    $services->set(MercureHubStub::class)
        ->decorate('mercure.hub.default', priority: 1)
        ->args([service('.inner')]);
};
