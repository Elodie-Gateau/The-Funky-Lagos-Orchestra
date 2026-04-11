<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260405200243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add constraint to tracks visibility';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE tracks ADD CONSTRAINT CK_tracks_visibility CHECK (is_visible = 0 OR status = 'Publié')");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE tracks DROP CONSTRAINT CK_tracks_visibility");

    }
}
