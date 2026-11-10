<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // The API's domain: lychen/config names its RabbitMQ queue and routing keys
    // (`flora.events`, `flora.internal.message.v1`) after it.
    $parameters->set('lychen.service', 'flora');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire();

    $services->load('App\\', __DIR__.'/../src/*')
        ->exclude([
            __DIR__.'/../src/{DependencyInjection,Entity,Migrations,SqlServer/Entity,Tests,Kernel.php}',
        ]);
};
