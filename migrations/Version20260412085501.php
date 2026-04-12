<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412085501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update phone to content';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting CHANGE phone content VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting CHANGE content phone VARCHAR(255) DEFAULT NULL');
    }
}
