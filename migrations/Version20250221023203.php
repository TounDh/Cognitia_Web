<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221023203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A61A76ED395');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A6171F7E88B');
        $this->addSql('ALTER TABLE event_participation DROP FOREIGN KEY FK_8F0C52E371F7E88B');
        $this->addSql('ALTER TABLE event_participation DROP FOREIGN KEY FK_8F0C52E3A76ED395');
        $this->addSql('DROP TABLE event_participants');
        $this->addSql('DROP TABLE event_participation');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_participants (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9C7A7A6171F7E88B (event_id), INDEX IDX_9C7A7A61A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE event_participation (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8F0C52E371F7E88B (event_id), INDEX IDX_8F0C52E3A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A61A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A6171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participation ADD CONSTRAINT FK_8F0C52E371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participation ADD CONSTRAINT FK_8F0C52E3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
