<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\User\Role;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211026142820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD supervisor CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494D9192F8 FOREIGN KEY (supervisor) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494D9192F8 ON user (supervisor)');
        $this->addSql('ALTER TABLE user_rank ADD type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:user.role)\'');
        $this->addSql('ALTER TABLE zone ADD type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:user.role)\'');
        $this->addSql('UPDATE user_rank SET type = :role', ['role' => Role::OPERATOR]);
        $this->addSql('UPDATE zone SET type = :role', ['role' => Role::OPERATOR]);
        $this->addSql('INSERT INTO callcenter.zone (id, name, color, description, hint, range_start, range_end, active, priority, type) VALUES 
        (UUID(), \'Изумрудная\', \'#34B308\', \'\', \'\', 0, 50, 1, 100, :role),
        (UUID(), \'Желтая\', \'#FFC300\', \'\', \'\', 50, 100, 1, 90, :role)', ['role' => Role::SUPERVISOR]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494D9192F8');
        $this->addSql('DROP INDEX IDX_8D93D6494D9192F8 ON user');
        $this->addSql('ALTER TABLE user DROP supervisor');
        $this->addSql('ALTER TABLE user_rank DROP type');
        $this->addSql('ALTER TABLE zone DROP type');
    }
}
