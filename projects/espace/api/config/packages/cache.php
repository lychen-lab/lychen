<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'cache' => [
            'app' => 'cache.adapter.redis',
            'default_redis_provider' => '%env(resolve:REDIS_URL)%',
            'pools' => [
                'cache.flysystem.psr6' => [
                    'adapter' => 'cache.app',
                ],
                'doctrine.query_cache_pool' => [
                    'adapter' => 'cache.app',
                ],
                'doctrine.result_cache_pool' => [
                    'adapter' => 'cache.app',
                ],
            ],
        ],
    ]);
};
