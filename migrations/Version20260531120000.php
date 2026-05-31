<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260531120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add home_position and album_position to track';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track ADD home_position INT DEFAULT NULL, ADD album_position INT DEFAULT NULL');
        $this->addSql('UPDATE track AS t1 JOIN (SELECT a.id, COUNT(b.id) AS cnt FROM track AS a INNER JOIN track AS b ON b.album_id = a.album_id AND b.id <= a.id GROUP BY a.id) AS sub ON t1.id = sub.id SET t1.album_position = sub.cnt');
        $this->addSql('UPDATE track AS t1 JOIN (SELECT a.id, COUNT(b.id) AS cnt FROM track AS a INNER JOIN track AS b ON b.visibility = 1 AND b.id <= a.id WHERE a.visibility = 1 GROUP BY a.id) AS sub ON t1.id = sub.id SET t1.home_position = sub.cnt');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE track DROP COLUMN home_position, DROP COLUMN album_position');
    }
}
