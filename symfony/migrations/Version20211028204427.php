<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028204427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE line_boulder (id INT AUTO_INCREMENT NOT NULL, rock_image_id INT NOT NULL, line_as_image_id INT NOT NULL, INDEX IDX_64DE75509A470729 (rock_image_id), UNIQUE INDEX UNIQ_64DE755025A932CF (line_as_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE75509A470729 FOREIGN KEY (rock_image_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE755025A932CF FOREIGN KEY (line_as_image_id) REFERENCES media (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE line_boulder');
    }
}
