<?php

declare(strict_types=1);

namespace DoctrineDefaultMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260829140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename area_request indexes to match Doctrine default hash-based names (doctrine:schema:validate was failing).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER INDEX idx_area_request_requester RENAME TO IDX_3847E662ED442CF4');
        $this->addSql('ALTER INDEX idx_area_request_area_activity_request RENAME TO IDX_75B6888ACD8E5A1F');
        $this->addSql('ALTER INDEX idx_area_request_area_activity_activity RENAME TO IDX_75B6888AAAFBCAE6');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER INDEX idx_75b6888aaafbcae6 RENAME TO IDX_AREA_REQUEST_AREA_ACTIVITY_ACTIVITY');
        $this->addSql('ALTER INDEX idx_75b6888acd8e5a1f RENAME TO IDX_AREA_REQUEST_AREA_ACTIVITY_REQUEST');
        $this->addSql('ALTER INDEX idx_3847e662ed442cf4 RENAME TO IDX_AREA_REQUEST_REQUESTER');
    }
}
