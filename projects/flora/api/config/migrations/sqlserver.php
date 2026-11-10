<?php

declare(strict_types=1);

/*
 * Migrations of the `sqlserver` entity manager. DoctrineMigrationsBundle holds one
 * configuration only, the default (Postgres) one in config/packages/doctrine_migrations.php,
 * so every migrations command for SQL Server takes this file and the entity manager:
 *
 *     bin/console doctrine:migrations:diff --configuration=config/migrations/sqlserver.php --em=sqlserver
 *     bin/console doctrine:migrations:migrate --configuration=config/migrations/sqlserver.php --em=sqlserver
 *
 * The entrypoint runs the second one at boot whenever SQLSERVER_DATABASE_URL is set.
 * Same storage table as the default configuration: the bundle hides that table name
 * from the schema tools of every connection.
 */
return [
    'migrations_paths' => [
        'DoctrineSqlServerMigrations' => __DIR__.'/../../migrations/SqlServer',
    ],
    'all_or_nothing' => false,
    'table_storage' => [
        'executed_at_column_name' => 'executed_at',
        'table_name' => 'doctrine_migration_versions',
        'version_column_length' => 191,
        'version_column_name' => 'version',
    ],
];
