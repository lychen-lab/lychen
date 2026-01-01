<?php

declare(strict_types=1);

use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Postgresql\JsonbContains;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'connections' => [
                'default' => [
                    'driver' => 'pdo_pgsql',
                    'url' => '%env(resolve:DATABASE_URL)%',
                ],
            ],
            'default_connection' => 'default',
        ],
        'orm' => [
            'controller_resolver' => [
                'auto_mapping' => false,
            ],
            'default_entity_manager' => 'default',
            'entity_managers' => [
                'default' => [
                    'connection' => 'default',
                    'mappings' => [
                        'Default' => [
                            'alias' => 'Default',
                            'dir' => '%kernel.project_dir%/src/Entity',
                            'is_bundle' => false,
                            'prefix' => 'App\Entity',
                            'type' => 'attribute',
                        ],
                    ],
                    'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                    'dql' => [
                        'string_functions' => [
                            'JSON_CONTAINS' => JsonbContains::class,
                        ],
                    ],
                ],
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'auto_generate_proxy_classes' => false,
                'proxy_dir' => '%kernel.build_dir%/doctrine/orm/Proxies',
                'query_cache_driver' => [
                    'pool' => 'doctrine.system_cache_pool',
                    'type' => 'pool',
                ],
                'result_cache_driver' => [
                    'pool' => 'doctrine.result_cache_pool',
                    'type' => 'pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
                'schema_filter' => '~^(?!(doctrine_|messenger_))~',
            ],
        ]);
    }
};
