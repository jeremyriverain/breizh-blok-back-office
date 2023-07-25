<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230219204726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder_area ADD parking_location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CD5AE2EF5A FOREIGN KEY (parking_location_id) REFERENCES geo_point (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51F1B1CD5AE2EF5A ON boulder_area (parking_location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder_area DROP FOREIGN KEY FK_51F1B1CD5AE2EF5A');
        $this->addSql('DROP INDEX UNIQ_51F1B1CD5AE2EF5A ON boulder_area');
        $this->addSql('ALTER TABLE boulder_area DROP parking_location_id');
    }
}
