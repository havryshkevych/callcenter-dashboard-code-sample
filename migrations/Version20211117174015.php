<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211117174015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users_dialogs (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', dialog_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_48F7DA6DA76ED395 (user_id), INDEX IDX_48F7DA6D5E46C4E2 (dialog_id), PRIMARY KEY(user_id, dialog_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scoring ADD user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');

        $this->addSql('INSERT INTO users_dialogs (user_id, dialog_id)
                            SELECT DISTINCT u.id AS user_id, d.id AS dialog_id
                            FROM dialog_record dr
                             JOIN dialog d ON dr.dialog_id = d.id
                             JOIN user u ON (u.chat_id = dr.sender_id OR u.call_id = dr.sender_id) AND
                                dr.sender = \'operator\'
                            WHERE u.id is not null');

        $this->addSql('UPDATE scoring s
                            JOIN dialog d ON s.dialog_id = d.id AND d.user_id IS NOT NULL
                            SET s.user_id = d.user_id');

        $this->addSql('ALTER TABLE users_dialogs ADD CONSTRAINT FK_48F7DA6DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_dialogs ADD CONSTRAINT FK_48F7DA6D5E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862A76ED395');
        $this->addSql('DROP INDEX IDX_4561D862A76ED395 ON dialog');
        $this->addSql('ALTER TABLE scoring DROP INDEX UNIQ_7B76A2CA5E46C4E2, ADD INDEX IDX_7B76A2CA5E46C4E2 (dialog_id)');

        $this->addSql('ALTER TABLE dialog DROP user_id');
        $this->addSql('ALTER TABLE scoring ADD CONSTRAINT FK_7B76A2CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7B76A2CAA76ED395 ON scoring (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog ADD user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('UPDATE dialog d1
                                JOIN (
                                    SELECT DISTINCT d.id AS dialog_id, ANY_VALUE(u.id) AS user_id
                                    FROM dialog_record dr
                                             JOIN dialog d ON dr.dialog_id = d.id
                                             JOIN user u ON (u.chat_id = dr.sender_id OR u.call_id = dr.sender_id) AND
                                                            dr.sender = \'operator\'
                                    WHERE u.id is not null
                                    GROUP BY dialog_id
                                ) d2 ON d1.id = d2.dialog_id
                            SET d1.user_id = d2.user_id');
        $this->addSql('DELETE t1 FROM scoring t1
                            INNER JOIN scoring t2
                            WHERE (t1.created_at < t2.created_at OR t1.updated_at < t2.updated_at) AND t1.dialog_id = t2.dialog_id');
        $this->addSql('DROP TABLE users_dialogs');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4561D862A76ED395 ON dialog (user_id)');
        $this->addSql('ALTER TABLE scoring DROP INDEX IDX_7B76A2CA5E46C4E2, ADD UNIQUE INDEX UNIQ_7B76A2CA5E46C4E2 (dialog_id)');
        $this->addSql('ALTER TABLE scoring DROP FOREIGN KEY FK_7B76A2CAA76ED395');
        $this->addSql('DROP INDEX IDX_7B76A2CAA76ED395 ON scoring');

        $this->addSql('ALTER TABLE scoring DROP user_id');
    }
}
