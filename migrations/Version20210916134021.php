<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210916134021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog ADD service_level_warning TINYINT(1) DEFAULT NULL, ADD service_level_average_answer_speed_warning TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE dialog ADD first_answer_speed INT DEFAULT NULL, ADD avarage_speed_answer INT DEFAULT NULL');
        $this->addSql('UPDATE dialog d
            JOIN dialog_record dr ON d.id = dr.dialog_id AND (dr.service_level_warning = true OR dr.service_level_average_answer_speed_warning = true) 
            SET d.service_level_warning = dr.service_level_warning,
            d.service_level_average_answer_speed_warning  = dr.service_level_average_answer_speed_warning');
        $this->addSql('ALTER TABLE dialog_record DROP seconds, DROP service_level_warning, DROP service_level_average_answer_speed_warning');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dialog DROP service_level_warning, DROP service_level_average_answer_speed_warning');
        $this->addSql('ALTER TABLE dialog DROP first_answer_speed, DROP avarage_speed_answer');
        $this->addSql('ALTER TABLE dialog_record ADD seconds INT DEFAULT NULL, ADD service_level_warning TINYINT(1) DEFAULT NULL, ADD service_level_average_answer_speed_warning TINYINT(1) DEFAULT NULL');
    }
}
