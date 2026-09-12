<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260824120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Link an AreaProposal to its proposer (Person).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE area_proposal ADD proposer_id INT NOT NULL');
        $this->addSql('ALTER TABLE area_proposal ADD CONSTRAINT FK_AREA_PROPOSAL_PROPOSER FOREIGN KEY (proposer_id) REFERENCES person (id)');
        // Named to match Doctrine's default hash-based index name (see doctrine:schema:validate),
        // instead of the FK_/IDX_AREA_PROPOSAL_PROPOSER convention used elsewhere in this codebase.
        $this->addSql('CREATE INDEX IDX_7CEACABAB13FA634 ON area_proposal (proposer_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE area_proposal DROP CONSTRAINT FK_AREA_PROPOSAL_PROPOSER');
        $this->addSql('DROP INDEX IDX_7CEACABAB13FA634');
        $this->addSql('ALTER TABLE area_proposal DROP proposer_id');
    }
}
