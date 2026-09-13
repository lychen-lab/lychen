<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mercure\Jwt\LcobucciFactory;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('cache_adapter', 'cache.adapter.system');

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
    // handed to a browser can never be used to publish. Valid for an hour.
    $services->set('app.mercure.subscriber_token_factory', LcobucciFactory::class)
        ->args(['%env(MERCURE_SUBSCRIBER_JWT_SECRET)%', 'hmac.sha256', 3600]);
};
