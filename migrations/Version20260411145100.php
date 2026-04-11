<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260411145100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Delete status from event';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP status');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD status VARCHAR(255) NOT NULL');
    }
}
