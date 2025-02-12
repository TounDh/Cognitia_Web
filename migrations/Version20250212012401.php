<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212012401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD quiz_id INT NOT NULL, ADD contenu LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B6F7494E853CD175 ON question (quiz_id)');
        $this->addSql('ALTER TABLE quiz ADD cours_id INT NOT NULL, ADD instructeur_id INT NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD temps_max INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9225FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A412FA927ECF78B0 ON quiz (cours_id)');
        $this->addSql('CREATE INDEX IDX_A412FA9225FCA809 ON quiz (instructeur_id)');
        $this->addSql('ALTER TABLE reponse ADD question_id INT NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD est_correcte TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5FB6DEC71E27F6BF ON reponse (question_id)');
        $this->addSql('ALTER TABLE resultat ADD quiz_id INT NOT NULL, ADD apprenant_id INT NOT NULL, ADD score INT NOT NULL');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E7DB5DE2853CD175 ON resultat (quiz_id)');
        $this->addSql('CREATE INDEX IDX_E7DB5DE2C5697D6D ON resultat (apprenant_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE nom nom VARCHAR(100) DEFAULT NULL, CHANGE prenom prenom VARCHAR(100) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('DROP INDEX IDX_B6F7494E853CD175 ON question');
        $this->addSql('ALTER TABLE question DROP quiz_id, DROP contenu');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9225FCA809');
        $this->addSql('DROP INDEX IDX_A412FA927ECF78B0 ON quiz');
        $this->addSql('DROP INDEX IDX_A412FA9225FCA809 ON quiz');
        $this->addSql('ALTER TABLE quiz DROP cours_id, DROP instructeur_id, DROP titre, DROP temps_max');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('DROP INDEX IDX_5FB6DEC71E27F6BF ON reponse');
        $this->addSql('ALTER TABLE reponse DROP question_id, DROP contenu, DROP est_correcte');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2853CD175');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2C5697D6D');
        $this->addSql('DROP INDEX IDX_E7DB5DE2853CD175 ON resultat');
        $this->addSql('DROP INDEX IDX_E7DB5DE2C5697D6D ON resultat');
        $this->addSql('ALTER TABLE resultat DROP quiz_id, DROP apprenant_id, DROP score');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE nom nom VARCHAR(100) DEFAULT \'NULL\', CHANGE prenom prenom VARCHAR(100) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
