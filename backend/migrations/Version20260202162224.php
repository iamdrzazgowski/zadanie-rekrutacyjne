<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202162224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_4C62E638A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C62E638A76ED395 ON contact (user_id)');
        $this->addSql('CREATE TABLE experience (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_590C103A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_590C103A76ED395 ON experience (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATE NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE user');
    }
}
