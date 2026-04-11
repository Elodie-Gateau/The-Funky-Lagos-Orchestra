<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260411141248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add name to event';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD name VARCHAR(255) NOT NULL, CHANGE date date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP name, CHANGE date date DATE DEFAULT NULL');
    }
}
