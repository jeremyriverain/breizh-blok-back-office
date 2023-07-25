<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028204613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder ADD boulder_id INT NOT NULL');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE755087658A6F FOREIGN KEY (boulder_id) REFERENCES boulder (id)');
        $this->addSql('CREATE INDEX IDX_64DE755087658A6F ON line_boulder (boulder_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder DROP FOREIGN KEY FK_64DE755087658A6F');
        $this->addSql('DROP INDEX IDX_64DE755087658A6F ON line_boulder');
        $this->addSql('ALTER TABLE line_boulder DROP boulder_id');
    }
}
