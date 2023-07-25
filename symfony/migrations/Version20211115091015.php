<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115091015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder_area ADD municipality_id INT NOT NULL');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CDAE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id)');
        $this->addSql('CREATE INDEX IDX_51F1B1CDAE6F181C ON boulder_area (municipality_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder_area DROP FOREIGN KEY FK_51F1B1CDAE6F181C');
        $this->addSql('DROP INDEX IDX_51F1B1CDAE6F181C ON boulder_area');
        $this->addSql('ALTER TABLE boulder_area DROP municipality_id');
    }
}
