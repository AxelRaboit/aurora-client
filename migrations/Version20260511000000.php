<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260511000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename client_agencies table and seq_client_agency_id sequence to app_ prefix';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE client_agencies RENAME TO app_agencies');
        $this->addSql('ALTER SEQUENCE seq_client_agency_id RENAME TO seq_app_agency_id');
        $this->addSql('ALTER TABLE core_users DROP CONSTRAINT IF EXISTS fk_42028409cdeadb2a');
        $this->addSql('ALTER TABLE core_users ADD CONSTRAINT FK_42028409CDEADB2A FOREIGN KEY (agency_id) REFERENCES app_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_users DROP CONSTRAINT FK_42028409CDEADB2A');
        $this->addSql('ALTER TABLE core_users ADD CONSTRAINT fk_42028409cdeadb2a FOREIGN KEY (agency_id) REFERENCES client_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_agencies RENAME TO client_agencies');
        $this->addSql('ALTER SEQUENCE seq_app_agency_id RENAME TO seq_client_agency_id');
    }
}
