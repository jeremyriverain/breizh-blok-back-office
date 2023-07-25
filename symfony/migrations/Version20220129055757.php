<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220129055757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder_area ADD centroid_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CD12F75E7A FOREIGN KEY (centroid_id) REFERENCES geo_point (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51F1B1CD12F75E7A ON boulder_area (centroid_id)');
        $this->addSql('ALTER TABLE municipality ADD centroid_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F5662812F75E7A FOREIGN KEY (centroid_id) REFERENCES geo_point (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6F5662812F75E7A ON municipality (centroid_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder_area DROP FOREIGN KEY FK_51F1B1CD12F75E7A');
        $this->addSql('DROP INDEX UNIQ_51F1B1CD12F75E7A ON boulder_area');
        $this->addSql('ALTER TABLE boulder_area DROP centroid_id');
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F5662812F75E7A');
        $this->addSql('DROP INDEX UNIQ_C6F5662812F75E7A ON municipality');
        $this->addSql('ALTER TABLE municipality DROP centroid_id');
    }
}
