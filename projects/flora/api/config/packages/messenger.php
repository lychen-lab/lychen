<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => [
                'async' => '%env(MESSENGER_TRANSPORT_DSN)%/async',
                'failed' => 'doctrine://default?queue_name=failed',
                'sync' => 'sync://',
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('framework', [
            'messenger' => [
                'transports' => [
                    'failed' => '%env(MESSENGER_TRANSPORT_DSN)%/failed',
                    'sync' => '%env(MESSENGER_TRANSPORT_DSN)%/sync',
                ],
            ],
        ]);
    }
};
