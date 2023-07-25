<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211128201612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D17AF437B03A8386 ON boulder (created_by_id)');
        $this->addSql('ALTER TABLE boulder_area ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CDB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_51F1B1CDB03A8386 ON boulder_area (created_by_id)');
        $this->addSql('ALTER TABLE rock ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA2B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3749BBA2B03A8386 ON rock (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE boulder DROP FOREIGN KEY FK_D17AF437B03A8386');
        $this->addSql('DROP INDEX IDX_D17AF437B03A8386 ON boulder');
        $this->addSql('ALTER TABLE boulder DROP created_by_id');
        $this->addSql('ALTER TABLE boulder_area DROP FOREIGN KEY FK_51F1B1CDB03A8386');
        $this->addSql('DROP INDEX IDX_51F1B1CDB03A8386 ON boulder_area');
        $this->addSql('ALTER TABLE boulder_area DROP created_by_id');
        $this->addSql('ALTER TABLE rock DROP FOREIGN KEY FK_3749BBA2B03A8386');
        $this->addSql('DROP INDEX IDX_3749BBA2B03A8386 ON rock');
        $this->addSql('ALTER TABLE rock DROP created_by_id');
    }
}
