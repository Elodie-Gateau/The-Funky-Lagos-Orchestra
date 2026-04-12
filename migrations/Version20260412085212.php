<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260412085212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add phone to setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting ADD phone VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting DROP phone');
    }
}
