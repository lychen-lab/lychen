<?php

declare(strict_types=1);

use Sentry\State\HubInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'sentry' => [
                    'fill_extra_context' => true,
                    'hub_id' => HubInterface::class,
                    'level' => Monolog\Logger::ERROR,
                    'type' => 'sentry',
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
