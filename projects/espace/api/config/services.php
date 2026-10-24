<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mercure\Jwt\LcobucciFactory;
use Symfony\Component\Mercure\ProtocolVersion;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('locale', 'fr');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire();

    $services->load('App\\', __DIR__ . '/../src/*')
        ->exclude([
            __DIR__ . '/../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}',
        ]);

    // Signs the tokens browsers subscribe to the Mercure hub with. Its key is the tenant's
    // *subscriber* key, not the publisher one the bundle signs updates with, so a token
    // handed to a browser can never be used to publish. Valid for an hour, and minted
    // for the hub's pre-1.0 protocol (the factory defaults to 1.0), like the bundle's hub.
    $services->set('app.mercure.subscriber_token_factory', LcobucciFactory::class)
        ->args(['%env(MERCURE_SUBSCRIBER_JWT_SECRET)%', 'hmac.sha256', 3600, '', ProtocolVersion::Legacy]);
};
