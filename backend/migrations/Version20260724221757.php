<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260724221757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds Admin.must_change_password, used to force a password change after first login.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admin ADD must_change_password TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admin DROP must_change_password');
    }
}
