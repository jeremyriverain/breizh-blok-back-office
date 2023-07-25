<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210801201933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder ADD rock_id INT NOT NULL');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437B48CC24E FOREIGN KEY (rock_id) REFERENCES rock (id)');
        $this->addSql('CREATE INDEX IDX_D17AF437B48CC24E ON boulder (rock_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF437B48CC24E');
        $this->addSql('DROP INDEX IDX_D17AF437B48CC24E ON boulder');
        $this->addSql('ALTER TABLE boulder DROP rock_id');
    }
}
