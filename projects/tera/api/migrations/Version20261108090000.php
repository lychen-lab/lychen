<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261108090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add lychen/event-bus\'s outbox (outbox_event) and inbox (processed_event), and seed_stock_entry\'s variety and received_from_seed_lot (the flora seed lot it came from, received once).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE outbox_event (published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, type VARCHAR(255) NOT NULL, payload TEXT NOT NULL, headers JSON NOT NULL, occurred_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX outbox_event_published_at_idx ON outbox_event (published_at)');

        $this->addSql('CREATE TABLE processed_event (event_id UUID NOT NULL, type VARCHAR(255) NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (event_id))');
        $this->addSql('CREATE INDEX processed_event_processed_at_idx ON processed_event (processed_at)');

        $this->addSql('ALTER TABLE seed_stock_entry ADD variety VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE seed_stock_entry ADD received_from_seed_lot UUID DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3FC1134AF4BE4C1D ON seed_stock_entry (received_from_seed_lot)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_3FC1134AF4BE4C1D');
        $this->addSql('ALTER TABLE seed_stock_entry DROP received_from_seed_lot');
        $this->addSql('ALTER TABLE seed_stock_entry DROP variety');
        $this->addSql('DROP TABLE processed_event');
        $this->addSql('DROP TABLE outbox_event');
    }
}
