<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260815120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Turn area_request into a full resource: rename state to place, link a requester (Person), add matching criteria and activities.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE area_request RENAME COLUMN state TO place');
        $this->addSql('ALTER TABLE area_request ADD requester_id INT NOT NULL');
        $this->addSql('ALTER TABLE area_request ADD minimal_surface_requested INT DEFAULT NULL');
        $this->addSql('ALTER TABLE area_request ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE area_request ADD archived_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE area_request ADD CONSTRAINT FK_AREA_REQUEST_REQUESTER FOREIGN KEY (requester_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_AREA_REQUEST_REQUESTER ON area_request (requester_id)');

        $this->addSql('CREATE TABLE area_request_area_activity (area_request_id INT NOT NULL, area_activity_id INT NOT NULL, PRIMARY KEY (area_request_id, area_activity_id))');
        $this->addSql('CREATE INDEX IDX_AREA_REQUEST_AREA_ACTIVITY_REQUEST ON area_request_area_activity (area_request_id)');
        $this->addSql('CREATE INDEX IDX_AREA_REQUEST_AREA_ACTIVITY_ACTIVITY ON area_request_area_activity (area_activity_id)');
        $this->addSql('ALTER TABLE area_request_area_activity ADD CONSTRAINT FK_AREA_REQUEST_AREA_ACTIVITY_REQUEST FOREIGN KEY (area_request_id) REFERENCES area_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE area_request_area_activity ADD CONSTRAINT FK_AREA_REQUEST_AREA_ACTIVITY_ACTIVITY FOREIGN KEY (area_activity_id) REFERENCES area_activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE area_request_area_activity DROP CONSTRAINT FK_AREA_REQUEST_AREA_ACTIVITY_REQUEST');
        $this->addSql('ALTER TABLE area_request_area_activity DROP CONSTRAINT FK_AREA_REQUEST_AREA_ACTIVITY_ACTIVITY');
        $this->addSql('DROP TABLE area_request_area_activity');

        $this->addSql('ALTER TABLE area_request DROP CONSTRAINT FK_AREA_REQUEST_REQUESTER');
        $this->addSql('DROP INDEX IDX_AREA_REQUEST_REQUESTER');
        $this->addSql('ALTER TABLE area_request DROP requester_id');
        $this->addSql('ALTER TABLE area_request DROP minimal_surface_requested');
        $this->addSql('ALTER TABLE area_request DROP city');
        $this->addSql('ALTER TABLE area_request DROP archived_at');
        $this->addSql('ALTER TABLE area_request RENAME COLUMN place TO state');
    }
}
