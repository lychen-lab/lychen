<?php

declare(strict_types=1);

use Monolog\Logger;
use Sentry\Monolog\Handler;
use Sentry\State\HubInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ($containerConfigurator->env() === 'prod') {
        // monolog-bundle 4 removed the built-in "sentry" handler type. Register the
        // Sentry handler as a service and wire it through a "service" handler instead.
        // https://docs.sentry.io/platforms/php/guides/symfony/integrations/monolog/
        $containerConfigurator->services()
            ->set(Handler::class)
            ->arg('$hub', service(HubInterface::class))
            ->arg('$level', Logger::ERROR)
            ->arg('$fillExtraContext', true);

        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'sentry' => [
                    'id' => Handler::class,
                    'type' => 'service',
                ],
            ],
        ]);

        $containerConfigurator->extension('sentry', [
            'dsn' => '%env(SENTRY_DSN)%',
            'register_error_handler' => false,
            'register_error_listener' => false,
        ]);
    }
};
