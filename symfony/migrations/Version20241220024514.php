<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241220024514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE boulder_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE boulder_area_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE department_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE geo_point_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE grade_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE line_boulder_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE municipality_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rock_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_table_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE boulder (id INT NOT NULL, grade_id INT DEFAULT NULL, rock_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, is_urban BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D17AF437FE19A1A8 ON boulder (grade_id)');
        $this->addSql('CREATE INDEX IDX_D17AF437B48CC24E ON boulder (rock_id)');
        $this->addSql('CREATE INDEX IDX_D17AF437B03A8386 ON boulder (created_by_id)');
        $this->addSql('CREATE INDEX IDX_D17AF437896DBBDE ON boulder (updated_by_id)');
        $this->addSql('CREATE INDEX name_idx ON boulder (name)');
        $this->addSql('CREATE TABLE boulder_area (id INT NOT NULL, municipality_id INT NOT NULL, centroid_id INT DEFAULT NULL, parking_location_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51F1B1CDAE6F181C ON boulder_area (municipality_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51F1B1CD12F75E7A ON boulder_area (centroid_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_51F1B1CD5AE2EF5A ON boulder_area (parking_location_id)');
        $this->addSql('CREATE INDEX IDX_51F1B1CDB03A8386 ON boulder_area (created_by_id)');
        $this->addSql('CREATE INDEX IDX_51F1B1CD896DBBDE ON boulder_area (updated_by_id)');
        $this->addSql('CREATE TABLE department (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE geo_point (id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE grade (id INT NOT NULL, name VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE line_boulder (id INT NOT NULL, rock_image_id INT NOT NULL, boulder_id INT NOT NULL, smooth_line TEXT NOT NULL, arr_arr_points JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64DE75509A470729 ON line_boulder (rock_image_id)');
        $this->addSql('CREATE INDEX IDX_64DE755087658A6F ON line_boulder (boulder_id)');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, rock_id INT DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image_dimensions TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A2CA10CB48CC24E ON media (rock_id)');
        $this->addSql('COMMENT ON COLUMN media.image_dimensions IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE municipality (id INT NOT NULL, centroid_id INT DEFAULT NULL, department_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6F5662812F75E7A ON municipality (centroid_id)');
        $this->addSql('CREATE INDEX IDX_C6F56628AE80F5DF ON municipality (department_id)');
        $this->addSql('CREATE TABLE rock (id INT NOT NULL, location_id INT NOT NULL, boulder_area_id INT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3749BBA264D218E ON rock (location_id)');
        $this->addSql('CREATE INDEX IDX_3749BBA2C8529D01 ON rock (boulder_area_id)');
        $this->addSql('CREATE INDEX IDX_3749BBA2B03A8386 ON rock (created_by_id)');
        $this->addSql('CREATE INDEX IDX_3749BBA2896DBBDE ON rock (updated_by_id)');
        $this->addSql('CREATE TABLE user_table (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, last_authenticated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14EB741EE7927C74 ON user_table (email)');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437B48CC24E FOREIGN KEY (rock_id) REFERENCES rock (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437B03A8386 FOREIGN KEY (created_by_id) REFERENCES user_table (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder ADD CONSTRAINT FK_D17AF437896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user_table (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CDAE6F181C FOREIGN KEY (municipality_id) REFERENCES municipality (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CD12F75E7A FOREIGN KEY (centroid_id) REFERENCES geo_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CD5AE2EF5A FOREIGN KEY (parking_location_id) REFERENCES geo_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CDB03A8386 FOREIGN KEY (created_by_id) REFERENCES user_table (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE boulder_area ADD CONSTRAINT FK_51F1B1CD896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user_table (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE75509A470729 FOREIGN KEY (rock_image_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE line_boulder ADD CONSTRAINT FK_64DE755087658A6F FOREIGN KEY (boulder_id) REFERENCES boulder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB48CC24E FOREIGN KEY (rock_id) REFERENCES rock (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F5662812F75E7A FOREIGN KEY (centroid_id) REFERENCES geo_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE municipality ADD CONSTRAINT FK_C6F56628AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA264D218E FOREIGN KEY (location_id) REFERENCES geo_point (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA2C8529D01 FOREIGN KEY (boulder_area_id) REFERENCES boulder_area (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA2B03A8386 FOREIGN KEY (created_by_id) REFERENCES user_table (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rock ADD CONSTRAINT FK_3749BBA2896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user_table (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE boulder_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE boulder_area_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE department_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE geo_point_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE grade_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE line_boulder_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE municipality_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rock_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_table_id_seq CASCADE');
        $this->addSql('ALTER TABLE boulder DROP CONSTRAINT FK_D17AF437FE19A1A8');
        $this->addSql('ALTER TABLE boulder DROP CONSTRAINT FK_D17AF437B48CC24E');
        $this->addSql('ALTER TABLE boulder DROP CONSTRAINT FK_D17AF437B03A8386');
        $this->addSql('ALTER TABLE boulder DROP CONSTRAINT FK_D17AF437896DBBDE');
        $this->addSql('ALTER TABLE boulder_area DROP CONSTRAINT FK_51F1B1CDAE6F181C');
        $this->addSql('ALTER TABLE boulder_area DROP CONSTRAINT FK_51F1B1CD12F75E7A');
        $this->addSql('ALTER TABLE boulder_area DROP CONSTRAINT FK_51F1B1CD5AE2EF5A');
        $this->addSql('ALTER TABLE boulder_area DROP CONSTRAINT FK_51F1B1CDB03A8386');
        $this->addSql('ALTER TABLE boulder_area DROP CONSTRAINT FK_51F1B1CD896DBBDE');
        $this->addSql('ALTER TABLE line_boulder DROP CONSTRAINT FK_64DE75509A470729');
        $this->addSql('ALTER TABLE line_boulder DROP CONSTRAINT FK_64DE755087658A6F');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CB48CC24E');
        $this->addSql('ALTER TABLE municipality DROP CONSTRAINT FK_C6F5662812F75E7A');
        $this->addSql('ALTER TABLE municipality DROP CONSTRAINT FK_C6F56628AE80F5DF');
        $this->addSql('ALTER TABLE rock DROP CONSTRAINT FK_3749BBA264D218E');
        $this->addSql('ALTER TABLE rock DROP CONSTRAINT FK_3749BBA2C8529D01');
        $this->addSql('ALTER TABLE rock DROP CONSTRAINT FK_3749BBA2B03A8386');
        $this->addSql('ALTER TABLE rock DROP CONSTRAINT FK_3749BBA2896DBBDE');
        $this->addSql('DROP TABLE boulder');
        $this->addSql('DROP TABLE boulder_area');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE geo_point');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE line_boulder');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE municipality');
        $this->addSql('DROP TABLE rock');
        $this->addSql('DROP TABLE user_table');
    }
}
