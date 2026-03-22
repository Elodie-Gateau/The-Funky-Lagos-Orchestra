<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260322162736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add constraint to name setting newValue: MusicTags';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE setting DROP CONSTRAINT CK_setting_name");
        $this->addSql("ALTER TABLE setting ADD CONSTRAINT CK_setting_name CHECK (name IN ('Photo', 'Description', 'MusicTags'))");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE setting DROP CONSTRAINT CK_setting_name");
        $this->addSql("ALTER TABLE setting ADD CONSTRAINT CK_setting_name CHECK (name IN ('Photo', 'Description'))");
    }
}

