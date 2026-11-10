<?php

namespace App\SqlServer\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * Every SQL Server database holds one schema per fixed database role (db_owner,
 * db_datareader...), and DBAL 4 reads them as namespaces of the database, whatever
 * the schema_filter. The mapping declares none, so each
 * `doctrine:migrations:diff --em=sqlserver` wrote their CREATE SCHEMA into down(), and
 * an otherwise empty migration when nothing had changed. Declaring them on the schema
 * built from the mapping makes both sides agree.
 *
 * Only for the diff tools: `doctrine:schema:create --em=sqlserver` would now try to
 * create them, but this database's tables come from migrations. Delete this listener
 * with DBAL 5, which derives namespaces from tables and drops createNamespace().
 */
#[AsDoctrineListener(event: ToolEvents::postGenerateSchema, connection: 'sqlserver')]
final class BuiltInSchemasListener
{
    private const array SCHEMAS = [
        'db_accessadmin',
        'db_backupoperator',
        'db_datareader',
        'db_datawriter',
        'db_ddladmin',
        'db_denydatareader',
        'db_denydatawriter',
        'db_owner',
        'db_securityadmin',
    ];

    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schema = $args->getSchema();
        foreach (self::SCHEMAS as $name) {
            if (!$schema->hasNamespace($name)) {
                $schema->createNamespace($name);
            }
        }
    }
}
