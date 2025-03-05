<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301152518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_participants (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9C7A7A6171F7E88B (event_id), INDEX IDX_9C7A7A61A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A6171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A61A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participation DROP FOREIGN KEY FK_8F0C52E371F7E88B');
        $this->addSql('ALTER TABLE event_participation DROP FOREIGN KEY FK_8F0C52E3A76ED395');
        $this->addSql('DROP TABLE event_participation');
        $this->addSql('ALTER TABLE commentaire MODIFY id_commentaire INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON commentaire');
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME NOT NULL, CHANGE id_commentaire id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE cours ADD instructeur_id INT NOT NULL, ADD difficulte VARCHAR(255) NOT NULL, ADD prix DOUBLE PRECISION NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C25FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C25FCA809 ON cours (instructeur_id)');
        $this->addSql('ALTER TABLE defis ADD cours_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD points_recompense INT NOT NULL, ADD badge_recompense VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE defis ADD CONSTRAINT FK_9701665B7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_9701665B7ECF78B0 ON defis (cours_id)');
        $this->addSql('ALTER TABLE evaluation ADD cours_id INT NOT NULL');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_1323A5757ECF78B0 ON evaluation (cours_id)');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE modules ADD cours_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD duree INT NOT NULL');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D77ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_2EB743D77ECF78B0 ON modules (cours_id)');
        $this->addSql('ALTER TABLE question ADD quiz_id INT NOT NULL, ADD contenu LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B6F7494E853CD175 ON question (quiz_id)');
        $this->addSql('ALTER TABLE quiz ADD cours_id INT NOT NULL, ADD instructeur_id INT NOT NULL, ADD apprenant_id INT DEFAULT NULL, ADD titre VARCHAR(255) NOT NULL, ADD temps_max INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9225FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A412FA927ECF78B0 ON quiz (cours_id)');
        $this->addSql('CREATE INDEX IDX_A412FA9225FCA809 ON quiz (instructeur_id)');
        $this->addSql('CREATE INDEX IDX_A412FA92C5697D6D ON quiz (apprenant_id)');
        $this->addSql('ALTER TABLE reponse ADD question_id INT NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD est_correcte TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5FB6DEC71E27F6BF ON reponse (question_id)');
        $this->addSql('ALTER TABLE resultat ADD quiz_id INT NOT NULL, ADD apprenant_id INT NOT NULL, ADD score INT NOT NULL');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E7DB5DE2853CD175 ON resultat (quiz_id)');
        $this->addSql('CREATE INDEX IDX_E7DB5DE2C5697D6D ON resultat (apprenant_id)');
        $this->addSql('ALTER TABLE user ADD is_phone_verified TINYINT(1) NOT NULL, CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(20) DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_participation (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8F0C52E3A76ED395 (user_id), INDEX IDX_8F0C52E371F7E88B (event_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event_participation ADD CONSTRAINT FK_8F0C52E371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participation ADD CONSTRAINT FK_8F0C52E3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A6171F7E88B');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A61A76ED395');
        $this->addSql('DROP TABLE event_participants');
        $this->addSql('ALTER TABLE commentaire MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON commentaire');
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME DEFAULT \'current_timestamp()\' NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE id id_commentaire INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD PRIMARY KEY (id_commentaire)');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C25FCA809');
        $this->addSql('DROP INDEX IDX_FDCA8C9C25FCA809 ON cours');
        $this->addSql('ALTER TABLE cours DROP instructeur_id, DROP difficulte, DROP prix, CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE defis DROP FOREIGN KEY FK_9701665B7ECF78B0');
        $this->addSql('DROP INDEX IDX_9701665B7ECF78B0 ON defis');
        $this->addSql('ALTER TABLE defis DROP cours_id, DROP titre, DROP description, DROP points_recompense, DROP badge_recompense');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5757ECF78B0');
        $this->addSql('DROP INDEX IDX_1323A5757ECF78B0 ON evaluation');
        $this->addSql('ALTER TABLE evaluation DROP cours_id');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D77ECF78B0');
        $this->addSql('DROP INDEX IDX_2EB743D77ECF78B0 ON modules');
        $this->addSql('ALTER TABLE modules DROP cours_id, DROP titre, DROP contenu, DROP duree');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('DROP INDEX IDX_B6F7494E853CD175 ON question');
        $this->addSql('ALTER TABLE question DROP quiz_id, DROP contenu');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9225FCA809');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92C5697D6D');
        $this->addSql('DROP INDEX IDX_A412FA927ECF78B0 ON quiz');
        $this->addSql('DROP INDEX IDX_A412FA9225FCA809 ON quiz');
        $this->addSql('DROP INDEX IDX_A412FA92C5697D6D ON quiz');
        $this->addSql('ALTER TABLE quiz DROP cours_id, DROP instructeur_id, DROP apprenant_id, DROP titre, DROP temps_max');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('DROP INDEX IDX_5FB6DEC71E27F6BF ON reponse');
        $this->addSql('ALTER TABLE reponse DROP question_id, DROP contenu, DROP est_correcte');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2853CD175');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2C5697D6D');
        $this->addSql('DROP INDEX IDX_E7DB5DE2853CD175 ON resultat');
        $this->addSql('DROP INDEX IDX_E7DB5DE2C5697D6D ON resultat');
        $this->addSql('ALTER TABLE resultat DROP quiz_id, DROP apprenant_id, DROP score');
        $this->addSql('ALTER TABLE user DROP is_phone_verified, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE last_name last_name VARCHAR(255) DEFAULT \'NULL\', CHANGE phone_number phone_number VARCHAR(20) DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
