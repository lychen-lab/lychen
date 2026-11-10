<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@LychenConfig/config/packages/doctrine.php');

    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'connections' => [
                // SQL Server, through DBAL only: no entity manager maps it. Inject
                // it as `#[Target('sqlserver')] Doctrine\DBAL\Connection $connection`
                // (or a `Connection $sqlserverConnection` argument).
                'sqlserver' => [
                    'driver' => 'pdo_sqlsrv',
                    'url' => '%env(resolve:SQLSERVER_DATABASE_URL)%',
                ],
            ],
        ],
    ]);
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
