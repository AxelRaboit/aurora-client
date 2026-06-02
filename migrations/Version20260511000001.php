<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260511000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Repoint core_employees and core_plannings agency FK to app_agencies';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_employees DROP CONSTRAINT fk_f5e2f324cdeadb2a');
        $this->addSql('ALTER TABLE core_employees ADD CONSTRAINT FK_F5E2F324CDEADB2A FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');

        $this->addSql('ALTER TABLE core_plannings DROP CONSTRAINT fk_6431b9cdeadb2a');
        $this->addSql('ALTER TABLE core_plannings ADD CONSTRAINT FK_6431B9CDEADB2A FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_employees DROP CONSTRAINT FK_F5E2F324CDEADB2A');
        $this->addSql('ALTER TABLE core_employees ADD CONSTRAINT fk_f5e2f324cdeadb2a FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE core_plannings DROP CONSTRAINT FK_6431B9CDEADB2A');
        $this->addSql('ALTER TABLE core_plannings ADD CONSTRAINT fk_6431b9cdeadb2a FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
