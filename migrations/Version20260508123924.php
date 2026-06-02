<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260508123924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add client_agencies table extending Aurora Core Agency with code field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE seq_client_agency_id INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE client_agencies (id INT NOT NULL, name VARCHAR(150) NOT NULL, code VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');

        // Copy existing agency rows so FK references stay valid after the switch.
        $this->addSql('INSERT INTO client_agencies (id, name, created_at, updated_at) SELECT id, name, created_at, updated_at FROM core_agencies');
        $this->addSql("SELECT setval('seq_client_agency_id', GREATEST((SELECT COALESCE(MAX(id), 0) FROM client_agencies), 1))");

        $this->addSql('ALTER TABLE core_users DROP CONSTRAINT fk_42028409cdeadb2a');
        $this->addSql('ALTER TABLE core_users ADD CONSTRAINT FK_42028409CDEADB2A FOREIGN KEY (agency_id) REFERENCES client_agencies (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE core_users DROP CONSTRAINT FK_42028409CDEADB2A');
        $this->addSql('ALTER TABLE core_users ADD CONSTRAINT fk_42028409cdeadb2a FOREIGN KEY (agency_id) REFERENCES core_agencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE client_agencies');
        $this->addSql('DROP SEQUENCE seq_client_agency_id CASCADE');
    }
}
