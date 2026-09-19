<?php

declare(strict_types=1);

use App\Tests\Utils\MercureHubStub;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->alias('security', Security::class);

    // Tests never reach a real hub, see MercureHubStub.
    $services->set('mercure.hub.default', MercureHubStub::class)
        ->args(['%env(MERCURE_PUBLIC_URL)%']);
};
