<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328164935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status to tracks with constraint';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracks ADD status VARCHAR(255) NOT NULL');
        $this->addSql("ALTER TABLE tracks ADD CONSTRAINT CK_tracks_status CHECK (status IN ('Publié', 'Brouillon'))");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE tracks DROP CONSTRAINT CK_tracks_status");
        $this->addSql('ALTER TABLE tracks DROP status');
    }
}
