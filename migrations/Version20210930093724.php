<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210930093724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog_record CHANGE record session VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE dialog_record SET session = REPLACE(session, \'https://admin.sender.mobi/analytics/dialog/\', \'\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog_record CHANGE session record VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('UPDATE dialog_record SET record = CONCAT(\'https://admin.sender.mobi/analytics/dialog/\', record)');
    }
}
