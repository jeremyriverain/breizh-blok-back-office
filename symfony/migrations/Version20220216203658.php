<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220216203658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder DROP FOREIGN KEY FK_64DE7550ED1E8BDB');
        $this->addSql('DROP INDEX UNIQ_64DE7550ED1E8BDB ON line_boulder');
        $this->addSql('ALTER TABLE line_boulder DROP line_boulder_image_id, DROP line_boulder_base64');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder ADD line_boulder_image_id INT DEFAULT NULL, ADD line_boulder_base64 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE7550ED1E8BDB FOREIGN KEY (line_boulder_image_id) REFERENCES media (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64DE7550ED1E8BDB ON line_boulder (line_boulder_image_id)');
    }
}
