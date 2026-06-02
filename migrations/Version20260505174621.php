<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505174621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE seq_project_id INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE projects (id INT NOT NULL, reference VARCHAR(32) DEFAULT NULL, title VARCHAR(150) NOT NULL, description TEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A4AEA34913 ON projects (reference)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE seq_project_id CASCADE');
        $this->addSql('DROP INDEX UNIQ_5C93B3A4AEA34913');
        $this->addSql('DROP TABLE projects');
    }
}
