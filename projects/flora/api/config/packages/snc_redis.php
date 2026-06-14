<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('snc_redis', [
        'clients' => [
            'api_cache' => [
                'alias' => 'api_cache',
                'dsn' => '%env(REDIS_URL)%',
                'type' => 'predis',
            ],
            'default' => [
                'alias' => 'default',
                'dsn' => '%env(REDIS_URL)%',
                'logging' => true,
                'type' => 'predis',
            ],
            'doctrine_metadata_cache' => [
                'alias' => 'doctrine_metadata_cache',
                'dsn' => '%env(REDIS_URL)%',
                'type' => 'predis',
            ],
            'session' => [
                'alias' => 'session',
                'dsn' => '%env(REDIS_URL)%',
                'type' => 'predis',
            ],
        ],
    ]);
};
