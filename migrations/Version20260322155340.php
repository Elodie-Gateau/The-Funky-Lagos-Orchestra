<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260322155340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create relation between user and setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting ADD updated_by_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, DROP updated_by, DROP created_by');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9F74B898896DBBDE ON setting (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_9F74B898B03A8386 ON setting (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898896DBBDE');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898B03A8386');
        $this->addSql('DROP INDEX IDX_9F74B898896DBBDE ON setting');
        $this->addSql('DROP INDEX IDX_9F74B898B03A8386 ON setting');
        $this->addSql('ALTER TABLE setting ADD updated_by VARCHAR(255) DEFAULT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, DROP updated_by_id, DROP created_by_id');
    }
}
