<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240831185237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D17AF437896DBBDE ON boulder (updated_by_id)');
        $this->addSql('ALTER TABLE boulder_area ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CD896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_51F1B1CD896DBBDE ON boulder_area (updated_by_id)');
        $this->addSql('ALTER TABLE rock ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3749BBA2896DBBDE ON rock (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF437896DBBDE');
        $this->addSql('DROP INDEX IDX_D17AF437896DBBDE ON boulder');
        $this->addSql('ALTER TABLE boulder DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE boulder_area DROP FOREIGN KEY FK_51F1B1CD896DBBDE');
        $this->addSql('DROP INDEX IDX_51F1B1CD896DBBDE ON boulder_area');
        $this->addSql('ALTER TABLE boulder_area DROP updated_by_id, DROP updated_at');
        $this->addSql('ALTER TABLE rock DROP FOREIGN KEY FK_3749BBA2896DBBDE');
        $this->addSql('DROP INDEX IDX_3749BBA2896DBBDE ON rock');
        $this->addSql('ALTER TABLE rock DROP updated_by_id, DROP updated_at');
    }
}
