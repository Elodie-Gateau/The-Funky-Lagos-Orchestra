<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516143142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update album table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE album DROP tracks');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE album ADD tracks VARCHAR(255) NOT NULL');
    }
}
