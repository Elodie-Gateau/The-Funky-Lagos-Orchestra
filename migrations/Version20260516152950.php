<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516152950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add delete cascade to album/tracks';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY `FK_D6E3F8A61137ABCF`');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A61137ABCF');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT `FK_D6E3F8A61137ABCF` FOREIGN KEY (album_id) REFERENCES album (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
