<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516133349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add album table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, year INT NOT NULL, tracks VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_39986E435E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE album');
    }
}
