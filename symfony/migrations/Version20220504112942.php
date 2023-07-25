<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220504112942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F56628AE80F5DF');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE municipality DROP FOREIGN KEY FK_C6F56628AE80F5DF');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
