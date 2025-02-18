<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212154555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_participation (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8F0C52E371F7E88B (event_id), INDEX IDX_8F0C52E3A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_participation ADD CONSTRAINT FK_8F0C52E371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participation ADD CONSTRAINT FK_8F0C52E3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD evenement_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD date_actuelle DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE id id_commentaire INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCFD02F13 FOREIGN KEY (evenement_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_67F068BCFD02F13 ON commentaire (evenement_id)');
        $this->addSql('ALTER TABLE commentaire ADD PRIMARY KEY (id_commentaire)');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD titre VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD date_debut DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL, ADD lieu VARCHAR(255) DEFAULT NULL, ADD type VARCHAR(100) DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE nom nom VARCHAR(100) DEFAULT NULL, CHANGE prenom prenom VARCHAR(100) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_participation DROP FOREIGN KEY FK_8F0C52E371F7E88B');
        $this->addSql('ALTER TABLE event_participation DROP FOREIGN KEY FK_8F0C52E3A76ED395');
        $this->addSql('DROP TABLE event_participation');
        $this->addSql('ALTER TABLE commentaire MODIFY id_commentaire INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCFD02F13');
        $this->addSql('DROP INDEX IDX_67F068BCFD02F13 ON commentaire');
        $this->addSql('DROP INDEX `PRIMARY` ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP evenement_id, DROP titre, DROP contenu, DROP date_actuelle, CHANGE id_commentaire id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE event DROP titre, DROP description, DROP date_debut, DROP date_fin, DROP lieu, DROP type, DROP image');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE nom nom VARCHAR(100) DEFAULT \'NULL\', CHANGE prenom prenom VARCHAR(100) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
