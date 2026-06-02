<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Repoint core_markdown_notes.agency_id from core_agencies to app_agencies.
 *
 * Aurora-client switched the Agency entity from Aurora's `core_agencies`
 * to its own `app_agencies` (via Version20260508123924 + Version20260511000000).
 * Every new aurora-core entity carrying an Agency FK must be repointed
 * here — the markdown notes module was added upstream after the original
 * switch and was missed, so DemoFixtures fails with a FK violation on
 * a fresh DB.
 */
final class Version20260516170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Repoint core_markdown_notes agency FK to app_agencies';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_markdown_notes DROP CONSTRAINT IF EXISTS fk_650acc0bcdeadb2a');
        $this->addSql('ALTER TABLE core_markdown_notes ADD CONSTRAINT FK_650ACC0BCDEADB2A FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_markdown_notes DROP CONSTRAINT IF EXISTS fk_650acc0bcdeadb2a');
        $this->addSql('ALTER TABLE core_markdown_notes ADD CONSTRAINT FK_650ACC0BCDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }
}
