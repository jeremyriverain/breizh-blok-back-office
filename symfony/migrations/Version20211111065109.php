<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211111065109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder DROP FOREIGN KEY FK_64DE755025A932CF');
        $this->addSql('DROP INDEX UNIQ_64DE755025A932CF ON line_boulder');
        $this->addSql('ALTER TABLE line_boulder ADD line_boulder_as_base64 LONGTEXT NOT NULL, DROP line_as_image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE line_boulder ADD line_as_image_id INT NOT NULL, DROP line_boulder_as_base64');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE755025A932CF FOREIGN KEY (line_as_image_id) REFERENCES media (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64DE755025A932CF ON line_boulder (line_as_image_id)');
    }
}
