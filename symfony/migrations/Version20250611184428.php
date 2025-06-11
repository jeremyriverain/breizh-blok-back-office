<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611184428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD boulder_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67C87658A6F FOREIGN KEY (boulder_id) REFERENCES boulder (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5CE2F67C87658A6F ON boulder_feedback (boulder_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67C87658A6F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5CE2F67C87658A6F ON boulder_feedback
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP boulder_id
        SQL);
    }
}
