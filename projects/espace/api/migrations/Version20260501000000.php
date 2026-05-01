<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260501000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE area_proposal ADD soil_type VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE area_proposal ADD available_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE area_proposal ADD status VARCHAR(255) NOT NULL DEFAULT 'pending_validation'");
        $this->addSql("ALTER TABLE area_proposal ADD workflow_id VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE area_proposal DROP soil_type");
        $this->addSql("ALTER TABLE area_proposal DROP available_at");
        $this->addSql("ALTER TABLE area_proposal DROP status");
        $this->addSql("ALTER TABLE area_proposal DROP workflow_id");
    }
}
