<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@LychenConfig/config/packages/doctrine.php');

    // SQL Server, next to the shared Postgres `default` connection: the `sqlserver`
    // entity manager maps the entities of src/SqlServer/Entity on it (src/Entity
    // stays the default one's). Its migrations have their own configuration,
    // config/migrations/sqlserver.php. Raw DBAL is there too, as
    // `#[Target('sqlserver')] Doctrine\DBAL\Connection $connection`.
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'connections' => [
                'sqlserver' => [
                    'driver' => 'pdo_sqlsrv',
                    'url' => '%env(resolve:SQLSERVER_DATABASE_URL)%',
                ],
            ],
        ],
        'orm' => [
            'entity_managers' => [
                'sqlserver' => [
                    'connection' => 'sqlserver',
                    'mappings' => [
                        'SqlServer' => [
                            'alias' => 'SqlServer',
                            'dir' => '%kernel.project_dir%/src/SqlServer/Entity',
                            'is_bundle' => false,
                            'prefix' => 'App\SqlServer\Entity',
                            'type' => 'attribute',
                        ],
                    ],
                    'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                ],
            ],
        ],
    ]);
    if ('prod' === $containerConfigurator->env()) {
        // The pools the shared base declares for the default entity manager.
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'entity_managers' => [
                    'sqlserver' => [
                        'query_cache_driver' => [
                            'pool' => 'doctrine.system_cache_pool',
                            'type' => 'pool',
                        ],
                        'result_cache_driver' => [
                            'pool' => 'doctrine.result_cache_pool',
                            'type' => 'pool',
                        ],
                    ],
                ],
            ],
        ]);
    }
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'connections' => [
                    'sqlserver' => [
                        'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
                    ],
                ],
            ],
        ]);
    }
};
