<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516160330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update track visibility';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track CHANGE is_visible visibility TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track CHANGE visibility is_visible TINYINT NOT NULL');
    }
}
