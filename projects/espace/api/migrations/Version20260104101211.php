<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104101211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area_proposal_area_activity (area_proposal_id INT NOT NULL, area_activity_id INT NOT NULL, PRIMARY KEY (area_proposal_id, area_activity_id))');
        $this->addSql('CREATE INDEX IDX_4B9B9F56DF428A28 ON area_proposal_area_activity (area_proposal_id)');
        $this->addSql('CREATE INDEX IDX_4B9B9F56AAFBCAE6 ON area_proposal_area_activity (area_activity_id)');
        $this->addSql('ALTER TABLE area_proposal_area_activity ADD CONSTRAINT FK_4B9B9F56DF428A28 FOREIGN KEY (area_proposal_id) REFERENCES area_proposal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE area_proposal_area_activity ADD CONSTRAINT FK_4B9B9F56AAFBCAE6 FOREIGN KEY (area_activity_id) REFERENCES area_activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_proposal_area_activity DROP CONSTRAINT FK_4B9B9F56DF428A28');
        $this->addSql('ALTER TABLE area_proposal_area_activity DROP CONSTRAINT FK_4B9B9F56AAFBCAE6');
        $this->addSql('DROP TABLE area_proposal_area_activity');
    }
}
