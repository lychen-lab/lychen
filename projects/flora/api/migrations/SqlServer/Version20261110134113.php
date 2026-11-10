<?php

declare(strict_types=1);

namespace DoctrineSqlServerMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * SQL Server (`sqlserver` entity manager), see config/migrations/sqlserver.php.
 */
final class Version20261110134113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add plant_observation, the first entity on SQL Server: an int id and an API uuid (UNIQUEIDENTIFIER).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE plant_observation (species NVARCHAR(255) NOT NULL, observed_at DATETIME2(6) NOT NULL, latitude DOUBLE PRECISION, longitude DOUBLE PRECISION, notes VARCHAR(MAX) COLLATE Latin1_General_100_CI_AS_SC_UTF8, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), id INT IDENTITY NOT NULL, uuid UNIQUEIDENTIFIER NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15E89C40D17F50A6 ON plant_observation (uuid) WHERE uuid IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE plant_observation');
    }
}
