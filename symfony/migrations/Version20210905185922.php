<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210905185922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD rock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB48CC24E FOREIGN KEY (rock_id) REFERENCES rock (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10CB48CC24E ON media (rock_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CB48CC24E');
        $this->addSql('DROP INDEX IDX_6A2CA10CB48CC24E ON media');
        $this->addSql('ALTER TABLE media DROP rock_id');
    }
}
