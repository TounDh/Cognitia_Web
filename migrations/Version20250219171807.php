<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219171807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cours ADD instructeur_id INT NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD date_publication DATETIME NOT NULL, DROP image_data, CHANGE description description LONGTEXT NOT NULL, CHANGE duree duree INT NOT NULL, CHANGE difficulte difficulte VARCHAR(255) NOT NULL, CHANGE prix prix NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C25FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C25FCA809 ON cours (instructeur_id)');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME DEFAULT \'current_timestamp()\' NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C25FCA809');
        $this->addSql('DROP INDEX IDX_FDCA8C9C25FCA809 ON cours');
        $this->addSql('ALTER TABLE cours ADD image_data LONGTEXT DEFAULT NULL, DROP instructeur_id, DROP image, DROP date_publication, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE duree duree INT DEFAULT NULL, CHANGE difficulte difficulte VARCHAR(50) DEFAULT \'NULL\', CHANGE prix prix NUMERIC(10, 2) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
