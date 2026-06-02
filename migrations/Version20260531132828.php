<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove the example client modules (Platform\Agency override + Tracking module).
 *
 * Drops the `app_agencies` and `projects` tables (and their sequences), and
 * repoints every core agency FK from the removed client `app_agencies` table
 * back to aurora-core's default `core_agencies` (AgencyInterface now resolves
 * to the bundle's own concrete Agency again).
 */
final class Version20260531132828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove example client modules (Agency override + Tracking): drop app_agencies/projects, repoint agency FKs to core_agencies';
    }

    public function up(Schema $schema): void
    {
        // 1. Drop the FK constraints that still reference the client app_agencies table.
        $this->addSql('ALTER TABLE core_assistant_conversations DROP CONSTRAINT fk_c2dbed58cdeadb2a');
        $this->addSql('ALTER TABLE core_block_notes DROP CONSTRAINT fk_113bfe00cdeadb2a');
        $this->addSql('ALTER TABLE core_employees DROP CONSTRAINT fk_f5e2f324cdeadb2a');
        $this->addSql('ALTER TABLE core_markdown_notes DROP CONSTRAINT fk_650acc0bcdeadb2a');
        $this->addSql('ALTER TABLE core_plannings DROP CONSTRAINT fk_6431b9cdeadb2a');
        $this->addSql('ALTER TABLE core_post_it_notes DROP CONSTRAINT fk_bf08188ccdeadb2a');
        $this->addSql('ALTER TABLE core_users DROP CONSTRAINT fk_42028409cdeadb2a');

        // 2. Null out agency_id values that pointed at the removed client agencies,
        //    so the new core_agencies FK validates against the now-dangling references.
        $this->addSql('UPDATE core_assistant_conversations SET agency_id = NULL');
        $this->addSql('UPDATE core_block_notes SET agency_id = NULL');
        $this->addSql('UPDATE core_employees SET agency_id = NULL');
        $this->addSql('UPDATE core_markdown_notes SET agency_id = NULL');
        $this->addSql('UPDATE core_plannings SET agency_id = NULL');
        $this->addSql('UPDATE core_post_it_notes SET agency_id = NULL');
        $this->addSql('UPDATE core_users SET agency_id = NULL');

        // 3. Drop the example tables + their sequences.
        $this->addSql('DROP SEQUENCE seq_app_agency_id CASCADE');
        $this->addSql('DROP SEQUENCE seq_app_tracking_project_id CASCADE');
        $this->addSql('DROP TABLE app_agencies');
        $this->addSql('DROP TABLE projects');

        // 4. Re-point the agency FKs at aurora-core's default core_agencies table.
        $this->addSql('ALTER TABLE core_assistant_conversations ADD CONSTRAINT FK_C2DBED58CDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE core_block_notes ADD CONSTRAINT FK_113BFE00CDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE core_employees ADD CONSTRAINT FK_F5E2F324CDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE core_markdown_notes ADD CONSTRAINT FK_650ACC0BCDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE core_plannings ADD CONSTRAINT FK_6431B9CDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE core_post_it_notes ADD CONSTRAINT FK_BF08188CCDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE core_users ADD CONSTRAINT FK_42028409CDEADB2A FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE seq_app_agency_id INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seq_app_tracking_project_id INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_agencies (name VARCHAR(150) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id INT NOT NULL, code VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE projects (id INT NOT NULL, reference VARCHAR(32) DEFAULT NULL, number VARCHAR(50) NOT NULL, title VARCHAR(150) NOT NULL, description TEXT NOT NULL, status VARCHAR(20) NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_5c93b3a4aea34913 ON projects (reference)');
        $this->addSql('CREATE UNIQUE INDEX uniq_5c93b3a496901f54 ON projects (number)');
        $this->addSql('ALTER TABLE core_assistant_conversations DROP CONSTRAINT FK_C2DBED58CDEADB2A');
        $this->addSql('ALTER TABLE core_assistant_conversations ADD CONSTRAINT fk_c2dbed58cdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE core_block_notes DROP CONSTRAINT FK_113BFE00CDEADB2A');
        $this->addSql('ALTER TABLE core_block_notes ADD CONSTRAINT fk_113bfe00cdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE core_employees DROP CONSTRAINT FK_F5E2F324CDEADB2A');
        $this->addSql('ALTER TABLE core_employees ADD CONSTRAINT fk_f5e2f324cdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE core_markdown_notes DROP CONSTRAINT FK_650ACC0BCDEADB2A');
        $this->addSql('ALTER TABLE core_markdown_notes ADD CONSTRAINT fk_650acc0bcdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE core_plannings DROP CONSTRAINT FK_6431B9CDEADB2A');
        $this->addSql('ALTER TABLE core_plannings ADD CONSTRAINT fk_6431b9cdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE core_post_it_notes DROP CONSTRAINT FK_BF08188CCDEADB2A');
        $this->addSql('ALTER TABLE core_post_it_notes ADD CONSTRAINT fk_bf08188ccdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE core_users DROP CONSTRAINT FK_42028409CDEADB2A');
        $this->addSql('ALTER TABLE core_users ADD CONSTRAINT fk_42028409cdeadb2a FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
