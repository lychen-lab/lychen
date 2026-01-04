<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104090443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_proposal ADD surface_total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE area_proposal ADD surface_to_share INT DEFAULT NULL');
        $this->addSql('ALTER TABLE area_proposal ADD city VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_proposal DROP surface_total');
        $this->addSql('ALTER TABLE area_proposal DROP surface_to_share');
        $this->addSql('ALTER TABLE area_proposal DROP city');
    }
}
