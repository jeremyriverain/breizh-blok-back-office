<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612080626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67CB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67C896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5CE2F67C896DBBDE ON boulder_feedback
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5CE2F67CB03A8386 ON boulder_feedback
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD received_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD sent_by VARCHAR(255) NOT NULL, DROP created_by_id, DROP updated_by_id, DROP created_at, DROP updated_at
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP received_at, DROP sent_by
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5CE2F67C896DBBDE ON boulder_feedback (updated_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5CE2F67CB03A8386 ON boulder_feedback (created_by_id)
        SQL);
    }
}
