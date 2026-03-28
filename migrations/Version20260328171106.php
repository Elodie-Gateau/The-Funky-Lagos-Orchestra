<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328171106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change tracks duration to varchar';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracks CHANGE duration duration VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracks CHANGE duration duration INT NOT NULL');
    }
}
