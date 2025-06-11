<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611183949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67CA45BB98C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_info
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_5CE2F67CA45BB98C ON boulder_feedback
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD sent_by VARCHAR(255) NOT NULL, DROP sent_by_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_info (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD sent_by_id INT NOT NULL, DROP sent_by
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67CA45BB98C FOREIGN KEY (sent_by_id) REFERENCES user_info (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5CE2F67CA45BB98C ON boulder_feedback (sent_by_id)
        SQL);
    }
}
