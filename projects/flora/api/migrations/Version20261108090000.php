<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261108090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add lychen/event-bus\'s outbox (outbox_event) and inbox (processed_event), and seed_lot.reserved_by: the member a given lot goes to.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE outbox_event (published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, type VARCHAR(255) NOT NULL, payload TEXT NOT NULL, headers JSON NOT NULL, occurred_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX outbox_event_published_at_idx ON outbox_event (published_at)');

        $this->addSql('CREATE TABLE processed_event (event_id UUID NOT NULL, type VARCHAR(255) NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (event_id))');
        $this->addSql('CREATE INDEX processed_event_processed_at_idx ON processed_event (processed_at)');

        $this->addSql('ALTER TABLE seed_lot ADD reserved_by VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seed_lot DROP reserved_by');
        $this->addSql('DROP TABLE processed_event');
        $this->addSql('DROP TABLE outbox_event');
    }
}
