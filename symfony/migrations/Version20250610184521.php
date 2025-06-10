<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610184521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE boulder_feedback (id INT AUTO_INCREMENT NOT NULL, new_location_id INT DEFAULT NULL, new_grade_id INT DEFAULT NULL, sent_by_id INT NOT NULL, message LONGTEXT DEFAULT NULL, received_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_5CE2F67C944290F6 (new_location_id), INDEX IDX_5CE2F67CB9702D84 (new_grade_id), INDEX IDX_5CE2F67CA45BB98C (sent_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67C944290F6 FOREIGN KEY (new_location_id) REFERENCES geo_point (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67CB9702D84 FOREIGN KEY (new_grade_id) REFERENCES grade (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback ADD CONSTRAINT FK_5CE2F67CA45BB98C FOREIGN KEY (sent_by_id) REFERENCES user_info (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt DROP FOREIGN KEY FK_6DB1DFFB87658A6F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt DROP FOREIGN KEY FK_6DB1DFFB586DFF2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE boulder_attempt
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE boulder_attempt (id INT AUTO_INCREMENT NOT NULL, boulder_id INT NOT NULL, user_info_id INT NOT NULL, INDEX IDX_6DB1DFFB586DFF2 (user_info_id), INDEX IDX_6DB1DFFB87658A6F (boulder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt ADD CONSTRAINT FK_6DB1DFFB87658A6F FOREIGN KEY (boulder_id) REFERENCES boulder (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt ADD CONSTRAINT FK_6DB1DFFB586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67C944290F6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67CB9702D84
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_feedback DROP FOREIGN KEY FK_5CE2F67CA45BB98C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE boulder_feedback
        SQL);
    }
}
