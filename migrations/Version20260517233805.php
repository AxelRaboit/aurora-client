<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517233805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename FK constraints + index after Aurora 0.4 namespace promotion (Aurora\Core\Agency → Aurora\Module\Platform\Agency). Doctrine auto-derives constraint names from class hashes; the namespace move changed the hashes, hence the FK rename. Cosmetic only — no data touched.';
    }

    public function up(Schema $schema): void
    {
        // Skip "DROP TABLE messenger_messages" emitted by the diff: the table
        // is owned by Symfony Messenger Doctrine transport, not by ORM, and
        // dropping it would break the worker. Doctrine sees it as orphan
        // because no entity maps it.
        $this->addSql('ALTER TABLE core_assistant_conversations DROP CONSTRAINT fk_assistconv_agency');
        $this->addSql('ALTER TABLE core_assistant_conversations ADD CONSTRAINT FK_C2DBED58CDEADB2A FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER INDEX idx_assistconv_agency RENAME TO IDX_C2DBED58CDEADB2A');
        $this->addSql('ALTER TABLE core_block_notes DROP CONSTRAINT fk_113bfe00cdeadb2a');
        $this->addSql('ALTER TABLE core_block_notes ADD CONSTRAINT FK_113BFE00CDEADB2A FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_assistant_conversations DROP CONSTRAINT FK_C2DBED58CDEADB2A');
        $this->addSql('ALTER TABLE core_assistant_conversations ADD CONSTRAINT fk_assistconv_agency FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_c2dbed58cdeadb2a RENAME TO idx_assistconv_agency');
        $this->addSql('ALTER TABLE core_block_notes DROP CONSTRAINT FK_113BFE00CDEADB2A');
        $this->addSql('ALTER TABLE core_block_notes ADD CONSTRAINT fk_113bfe00cdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
