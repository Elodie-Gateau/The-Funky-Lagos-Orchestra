<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516134826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'rename tracks to track and add album column';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE track (id INT AUTO_INCREMENT NOT NULL, added_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, audio_file VARCHAR(255) NOT NULL, duration VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, album VARCHAR(255) NOT NULL, is_visible TINYINT NOT NULL, UNIQUE INDEX UNIQ_D6E3F8A62B36786B (title), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP TABLE tracks');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tracks (id INT AUTO_INCREMENT NOT NULL, added_at DATETIME NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, audio_file VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, duration VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, artist VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, album VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_visible TINYINT NOT NULL, UNIQUE INDEX UNIQ_246D2A2E2B36786B (title), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE track');
    }
}
