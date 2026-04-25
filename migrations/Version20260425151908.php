<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260425151908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove status column and its CHECK constraints from tracks table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracks DROP CHECK CK_tracks_visibility');
        $this->addSql('ALTER TABLE tracks DROP CHECK CK_tracks_status');
        $this->addSql('ALTER TABLE tracks DROP status');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracks ADD status VARCHAR(255) NOT NULL');
        $this->addSql("ALTER TABLE tracks ADD CONSTRAINT CK_tracks_status CHECK (status IN ('Publié', 'Brouillon'))");
        $this->addSql("ALTER TABLE tracks ADD CONSTRAINT CK_tracks_visibility CHECK (is_visible = 0 OR (is_visible = 1 AND status = 'Publié'))");
    }
}
