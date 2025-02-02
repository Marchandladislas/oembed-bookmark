<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202124433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bookmark, photo_bookmark and video_bookmark tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bookmark_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bookmark (id INT NOT NULL, url VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bookmark.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE photo_bookmark (id INT NOT NULL, width SMALLINT DEFAULT NULL, height SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE video_bookmark (id INT NOT NULL, width SMALLINT DEFAULT NULL, height SMALLINT DEFAULT NULL, duration INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE photo_bookmark ADD CONSTRAINT FK_495E8D97BF396750 FOREIGN KEY (id) REFERENCES bookmark (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_bookmark ADD CONSTRAINT FK_72E0A0A1BF396750 FOREIGN KEY (id) REFERENCES bookmark (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE bookmark_id_seq CASCADE');
        $this->addSql('ALTER TABLE photo_bookmark DROP CONSTRAINT FK_495E8D97BF396750');
        $this->addSql('ALTER TABLE video_bookmark DROP CONSTRAINT FK_72E0A0A1BF396750');
        $this->addSql('DROP TABLE bookmark');
        $this->addSql('DROP TABLE photo_bookmark');
        $this->addSql('DROP TABLE video_bookmark');
    }
}
