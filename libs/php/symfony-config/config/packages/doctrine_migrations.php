<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine_migrations', [
        'all_or_nothing' => false,
        'custom_template' => null,
        'migrations_paths' => [
            'DoctrineDefaultMigrations' => '%kernel.project_dir%/migrations',
        ],
        'organize_migrations' => false,
        'storage' => [
            'table_storage' => [
                'executed_at_column_name' => 'executed_at',
                'table_name' => 'doctrine_migration_versions',
                'version_column_length' => 191,
                'version_column_name' => 'version',
            ],
        ],
    ]);
};
