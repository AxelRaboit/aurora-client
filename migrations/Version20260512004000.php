<?php

declare(strict_types=1);

namespace ClientMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rename `app_tracking_admin` → `app_tracking_backend` to align with
 * the aurora-core `<m>_backend` / `<m>_frontend` symmetry convention
 * established by aurora-core Version20260512003006.
 *
 * Updates both core_settings.setting_key and the per-user
 * core_users.disabled_modules jsonb mask.
 */
final class Version20260512004000 extends AbstractMigration
{
    private const string OLD = 'app_tracking_admin';
    private const string NEW = 'app_tracking_backend';

    public function getDescription(): string
    {
        return 'Rename Tracking backend toggle key from app_tracking_admin to app_tracking_backend';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'UPDATE core_settings SET setting_key = :new WHERE setting_key = :old',
            ['new' => self::NEW, 'old' => self::OLD],
        );
        $this->addSql(
            'UPDATE core_users SET disabled_modules = REPLACE(disabled_modules::text, :old, :new)::jsonb WHERE disabled_modules::text LIKE :pattern',
            ['old' => '"'.self::OLD.'"', 'new' => '"'.self::NEW.'"', 'pattern' => '%"'.self::OLD.'"%'],
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'UPDATE core_settings SET setting_key = :old WHERE setting_key = :new',
            ['new' => self::NEW, 'old' => self::OLD],
        );
        $this->addSql(
            'UPDATE core_users SET disabled_modules = REPLACE(disabled_modules::text, :new, :old)::jsonb WHERE disabled_modules::text LIKE :pattern',
            ['old' => '"'.self::OLD.'"', 'new' => '"'.self::NEW.'"', 'pattern' => '%"'.self::NEW.'"%'],
        );
    }
}
