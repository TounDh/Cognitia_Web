<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306012409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz DROP INDEX IDX_A412FA927ECF78B0, ADD UNIQUE INDEX UNIQ_A412FA927ECF78B0 (cours_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE quiz DROP INDEX UNIQ_A412FA927ECF78B0, ADD INDEX IDX_A412FA927ECF78B0 (cours_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
