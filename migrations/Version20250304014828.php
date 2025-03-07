<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304014828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificat DROP FOREIGN KEY FK_27448F77A76ED395');
        $this->addSql('DROP INDEX IDX_27448F77A76ED395 ON certificat');
        $this->addSql('ALTER TABLE certificat CHANGE user_id apprenant_id INT NOT NULL');
        $this->addSql('ALTER TABLE certificat ADD CONSTRAINT FK_27448F77C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_27448F77C5697D6D ON certificat (apprenant_id)');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificat DROP FOREIGN KEY FK_27448F77C5697D6D');
        $this->addSql('DROP INDEX IDX_27448F77C5697D6D ON certificat');
        $this->addSql('ALTER TABLE certificat CHANGE apprenant_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE certificat ADD CONSTRAINT FK_27448F77A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_27448F77A76ED395 ON certificat (user_id)');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
