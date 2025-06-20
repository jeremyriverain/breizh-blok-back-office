<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620230019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67CB9702D84
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5CE2F67CB9702D84 ON boulder_feedback
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP new_grade_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD new_grade_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67CB9702D84 FOREIGN KEY (new_grade_id) REFERENCES grade (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5CE2F67CB9702D84 ON boulder_feedback (new_grade_id)
        SQL);
    }
}
