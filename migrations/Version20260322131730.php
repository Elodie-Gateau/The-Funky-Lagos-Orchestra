<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260322131730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add description to setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting ADD description_fr LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE setting ADD description_en LONGTEXT DEFAULT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting DROP description_fr');
        $this->addSql('ALTER TABLE setting DROP description_en');

    }
}
