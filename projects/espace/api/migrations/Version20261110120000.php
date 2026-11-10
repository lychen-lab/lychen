<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261110120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Listing moderation: store the rejection reason, and send rejected area requests back to draft (the "rejected" place is gone).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE area_proposal ADD rejection_reason TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE area_request ADD rejection_reason TEXT DEFAULT NULL');
        $this->addSql("UPDATE area_request SET place = 'draft' WHERE place = 'rejected'");
    }

    public function down(Schema $schema): void
    {
        // The requests moved from "rejected" to "draft" stay drafts: nothing tells them apart.
        $this->addSql('ALTER TABLE area_proposal DROP rejection_reason');
        $this->addSql('ALTER TABLE area_request DROP rejection_reason');
    }
}
