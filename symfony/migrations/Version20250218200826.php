<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218200826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF4374679B87C');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF4374679B87C FOREIGN KEY (height_id) REFERENCES height_boulder (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF4374679B87C');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF4374679B87C FOREIGN KEY (height_id) REFERENCES height_boulder (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
