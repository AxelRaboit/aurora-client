<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Carries aurora-core 0.9.149's category trash onto the client table.
 *
 * The bundle's migration adds `deleted_at` to `core_ged_document_categories`,
 * which this project does not use: its categories live in
 * `app_document_categories` since the entity was substituted to gain a `color`
 * column. The bundle cannot know that, so the half that lands on the client
 * table has to be written here - exactly like the substitution itself was.
 *
 * The slug's uniqueness becomes partial at the same time, and for the same
 * reason as in the bundle: a category waiting in the trash must not hold a
 * name hostage, or recreating one under that name fails on a constraint over a
 * row nothing displays.
 */
final class Version20260913100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Trash for the client document categories (aurora-core 0.9.149)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_document_categories ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_app_categories_deleted_at ON app_document_categories (deleted_at)');

        $this->addSql('DROP INDEX uniq_c58e05fb989d9b62');
        $this->addSql('CREATE UNIQUE INDEX uniq_app_category_slug_live ON app_document_categories (slug) WHERE deleted_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_app_category_slug_live');
        $this->addSql('CREATE UNIQUE INDEX uniq_c58e05fb989d9b62 ON app_document_categories (slug)');

        $this->addSql('DROP INDEX idx_app_categories_deleted_at');
        $this->addSql('ALTER TABLE app_document_categories DROP deleted_at');
    }
}
