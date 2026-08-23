<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Substitutes Aurora's document categories with the client entity, which adds
 * a `color` column.
 *
 * The generated diff created the new table and repointed the foreign key, and
 * stopped there. On any project that already has categories that is a broken
 * migration: `core_ged_documents` still holds the old ids, so adding the
 * constraint against an empty table fails outright - and if it did not, every
 * categorised document would be orphaned.
 *
 * The copy below is the missing half. Ids are carried over deliberately so the
 * documents keep pointing at the same rows, and the sequence is fast-forwarded
 * past them so the next insert does not collide.
 */
final class Version20260808114952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Swap Ged document categories to the client entity (adds color), carrying existing rows over';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE seq_app_document_category_id INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_document_categories (name VARCHAR(150) NOT NULL, slug VARCHAR(180) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id INT NOT NULL, color VARCHAR(7) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C58E05FB989D9B62 ON app_document_categories (slug)');

        // Carry the existing rows across, ids included, before anything points
        // at the new table. `color` starts null - no old row can have one.
        $this->addSql(<<<'SQL'
            INSERT INTO app_document_categories (id, name, slug, description, created_at, updated_at, color)
            SELECT id, name, slug, description, created_at, updated_at, NULL
            FROM core_ged_document_categories
            SQL);

        // Move the sequence past the copied ids, or the first insert collides
        // with a primary key that is already taken.
        $this->addSql(<<<'SQL'
            SELECT setval('seq_app_document_category_id', COALESCE((SELECT MAX(id) FROM app_document_categories), 0) + 1, false)
            SQL);

        $this->addSql('ALTER TABLE core_ged_documents DROP CONSTRAINT fk_a80b359a12469de2');
        $this->addSql('ALTER TABLE core_ged_documents ADD CONSTRAINT FK_A80B359A12469DE2 FOREIGN KEY (category_id) REFERENCES app_document_categories (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // Same care in reverse: put the rows back before the constraint that
        // needs them, otherwise rolling back orphans every categorised
        // document. `color` is dropped - Aurora's table has no such column.
        $this->addSql(<<<'SQL'
            INSERT INTO core_ged_document_categories (id, name, slug, description, created_at, updated_at)
            SELECT id, name, slug, description, created_at, updated_at
            FROM app_document_categories
            ON CONFLICT (id) DO NOTHING
            SQL);

        $this->addSql('ALTER TABLE core_ged_documents DROP CONSTRAINT FK_A80B359A12469DE2');
        $this->addSql('ALTER TABLE core_ged_documents ADD CONSTRAINT fk_a80b359a12469de2 FOREIGN KEY (category_id) REFERENCES core_ged_document_categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('DROP TABLE app_document_categories');
        $this->addSql('DROP SEQUENCE seq_app_document_category_id CASCADE');
    }
}
