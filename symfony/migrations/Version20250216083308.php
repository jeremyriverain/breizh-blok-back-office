<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216083308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE height_boulder (id INT AUTO_INCREMENT NOT NULL, min SMALLINT NOT NULL, max SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE boulder ADD height_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF4374679B87C FOREIGN KEY (height_id) REFERENCES height_boulder (id)');
        $this->addSql('CREATE INDEX IDX_D17AF4374679B87C ON boulder (height_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF4374679B87C');
        $this->addSql('DROP TABLE height_boulder');
        $this->addSql('DROP INDEX IDX_D17AF4374679B87C ON boulder');
        $this->addSql('ALTER TABLE boulder DROP height_id');
    }
}
