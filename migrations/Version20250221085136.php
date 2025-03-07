<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221085136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A6171F7E88B');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A61A76ED395');
        $this->addSql('DROP TABLE event_participants');
        $this->addSql('ALTER TABLE commentaire MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON commentaire');
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE id id_commentaire INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD PRIMARY KEY (id_commentaire)');
        $this->addSql('ALTER TABLE cours ADD difficulte VARCHAR(255) NOT NULL, ADD prix DOUBLE PRECISION NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE defis ADD cours_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD points_recompense INT NOT NULL, ADD badge_recompense VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE defis ADD CONSTRAINT FK_9701665B7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_9701665B7ECF78B0 ON defis (cours_id)');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE modules ADD cours_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD duree INT NOT NULL');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D77ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_2EB743D77ECF78B0 ON modules (cours_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_participants (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9C7A7A61A76ED395 (user_id), INDEX IDX_9C7A7A6171F7E88B (event_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A6171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A61A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire MODIFY id_commentaire INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON commentaire');
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME NOT NULL, CHANGE id_commentaire id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE cours DROP difficulte, DROP prix, CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE defis DROP FOREIGN KEY FK_9701665B7ECF78B0');
        $this->addSql('DROP INDEX IDX_9701665B7ECF78B0 ON defis');
        $this->addSql('ALTER TABLE defis DROP cours_id, DROP titre, DROP description, DROP points_recompense, DROP badge_recompense');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D77ECF78B0');
        $this->addSql('DROP INDEX IDX_2EB743D77ECF78B0 ON modules');
        $this->addSql('ALTER TABLE modules DROP cours_id, DROP titre, DROP contenu, DROP duree');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
