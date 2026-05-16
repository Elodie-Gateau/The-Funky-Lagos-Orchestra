<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260516144047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'fix album table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track ADD album_id INT DEFAULT NULL, DROP album');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_D6E3F8A61137ABCF ON track (album_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A61137ABCF');
        $this->addSql('DROP INDEX IDX_D6E3F8A61137ABCF ON track');
        $this->addSql('ALTER TABLE track ADD album VARCHAR(255) NOT NULL, DROP album_id');
    }
}
