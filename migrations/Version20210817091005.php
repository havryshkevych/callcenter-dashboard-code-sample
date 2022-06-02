<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210817091005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dialog (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', date DATETIME NOT NULL, type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dialog.type)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_4561D862A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dialog_record (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dialog_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', chat_id VARCHAR(255) DEFAULT NULL, record VARCHAR(255) DEFAULT NULL, received_at DATETIME DEFAULT NULL, seconds INT DEFAULT NULL, sender VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dialogRecord.sender)\', sender_id VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_88DB4D6E5E46C4E2 (dialog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', scoring_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', criteria_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', value DOUBLE PRECISION NOT NULL, comment VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1323A575DF2EDCBF (scoring_id), UNIQUE INDEX UNIQ_1323A575990BEA15 (criteria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation_criteria (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dialog.type)\', title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, sort INT DEFAULT NULL, active TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scoring (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dialog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', history JSON DEFAULT NULL, archived_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7B76A2CA5E46C4E2 (dialog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, call_id VARCHAR(255) DEFAULT NULL, chat_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64950A89B2C (call_id), UNIQUE INDEX UNIQ_8D93D6491A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dialog_record ADD CONSTRAINT FK_88DB4D6E5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575DF2EDCBF FOREIGN KEY (scoring_id) REFERENCES scoring (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575990BEA15 FOREIGN KEY (criteria_id) REFERENCES evaluation_criteria (id)');
        $this->addSql('ALTER TABLE scoring ADD CONSTRAINT FK_7B76A2CA5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog_record DROP FOREIGN KEY FK_88DB4D6E5E46C4E2');
        $this->addSql('ALTER TABLE scoring DROP FOREIGN KEY FK_7B76A2CA5E46C4E2');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575990BEA15');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575DF2EDCBF');
        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862A76ED395');
        $this->addSql('DROP TABLE dialog');
        $this->addSql('DROP TABLE dialog_record');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE evaluation_criteria');
        $this->addSql('DROP TABLE scoring');
        $this->addSql('DROP TABLE user');
    }
}
