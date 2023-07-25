<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210730210040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boulder (id INT AUTO_INCREMENT NOT NULL, grade_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D17AF437FE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boulder_area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE geo_point (id INT AUTO_INCREMENT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rock (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, boulder_area_id INT NOT NULL, UNIQUE INDEX UNIQ_3749BBA264D218E (location_id), INDEX IDX_3749BBA2C8529D01 (boulder_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA264D218E FOREIGN KEY (location_id) REFERENCES geo_point (id)');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA2C8529D01 FOREIGN KEY (boulder_area_id) REFERENCES boulder_area (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rock DROP FOREIGN KEY FK_3749BBA2C8529D01');
        $this->addSql('ALTER TABLE rock DROP FOREIGN KEY FK_3749BBA264D218E');
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF437FE19A1A8');
        $this->addSql('DROP TABLE boulder');
        $this->addSql('DROP TABLE boulder_area');
        $this->addSql('DROP TABLE geo_point');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE rock');
    }
}
