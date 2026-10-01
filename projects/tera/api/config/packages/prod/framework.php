<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'cache' => [
            'default_redis_provider' => 'snc_redis.default',
            'app' => 'cache.adapter.redis',
            'system' => 'cache.adapter.redis',
        ],
    ]);
};
