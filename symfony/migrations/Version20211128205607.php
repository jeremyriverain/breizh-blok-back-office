<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211128205607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder ADD line_boulder_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE7550ED1E8BDB FOREIGN KEY (line_boulder_image_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64DE7550ED1E8BDB ON line_boulder (line_boulder_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder DROP FOREIGN KEY FK_64DE7550ED1E8BDB');
        $this->addSql('DROP INDEX UNIQ_64DE7550ED1E8BDB ON line_boulder');
        $this->addSql('ALTER TABLE line_boulder DROP line_boulder_image_id');
    }
}
