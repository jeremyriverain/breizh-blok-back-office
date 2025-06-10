<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610135301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE boulder_attempt (id INT AUTO_INCREMENT NOT NULL, boulder_id INT NOT NULL, user_info_id INT NOT NULL, INDEX IDX_6DB1DFFB87658A6F (boulder_id), INDEX IDX_6DB1DFFB586DFF2 (user_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_info (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt ADD CONSTRAINT FK_6DB1DFFB87658A6F FOREIGN KEY (boulder_id) REFERENCES boulder (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt ADD CONSTRAINT FK_6DB1DFFB586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt DROP FOREIGN KEY FK_6DB1DFFB87658A6F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE boulder_attempt DROP FOREIGN KEY FK_6DB1DFFB586DFF2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE boulder_attempt
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_info
        SQL);
    }
}
