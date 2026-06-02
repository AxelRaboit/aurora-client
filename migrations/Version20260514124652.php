<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514124652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename project sequence seq_project_id → seq_app_tracking_project_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE seq_project_id CASCADE');
        $this->addSql('CREATE SEQUENCE seq_app_tracking_project_id INCREMENT BY 1 MINVALUE 1 START 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE seq_app_tracking_project_id CASCADE');
        $this->addSql('CREATE SEQUENCE seq_project_id INCREMENT BY 1 MINVALUE 1 START 1');
    }
}
