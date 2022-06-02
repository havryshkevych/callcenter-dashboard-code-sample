<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011123552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog_record DROP FOREIGN KEY FK_88DB4D6E5E46C4E2');
        $this->addSql('ALTER TABLE dialog_record ADD CONSTRAINT FK_88DB4D6E5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575DF2EDCBF');
        $this->addSql('ALTER TABLE evaluation CHANGE scoring_id scoring_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575DF2EDCBF FOREIGN KEY (scoring_id) REFERENCES scoring (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scoring DROP FOREIGN KEY FK_7B76A2CA5E46C4E2');
        $this->addSql('ALTER TABLE scoring CHANGE dialog_id dialog_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE scoring ADD CONSTRAINT FK_7B76A2CA5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog_record DROP FOREIGN KEY FK_88DB4D6E5E46C4E2');
        $this->addSql('ALTER TABLE dialog_record ADD CONSTRAINT FK_88DB4D6E5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575DF2EDCBF');
        $this->addSql('ALTER TABLE evaluation CHANGE scoring_id scoring_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575DF2EDCBF FOREIGN KEY (scoring_id) REFERENCES scoring (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE scoring DROP FOREIGN KEY FK_7B76A2CA5E46C4E2');
        $this->addSql('ALTER TABLE scoring CHANGE dialog_id dialog_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE scoring ADD CONSTRAINT FK_7B76A2CA5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
