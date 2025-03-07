<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228003654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, evenement_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_actuelle DATETIME NOT NULL, INDEX IDX_67F068BCFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, instructeur_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, date_publication DATETIME NOT NULL, duree INT NOT NULL, difficulte VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_FDCA8C9C25FCA809 (instructeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE defis (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, points_recompense INT NOT NULL, badge_recompense VARCHAR(255) NOT NULL, INDEX IDX_9701665B7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, INDEX IDX_1323A5757ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, lieu VARCHAR(255) DEFAULT NULL, type VARCHAR(100) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_participants (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9C7A7A6171F7E88B (event_id), INDEX IDX_9C7A7A61A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modules (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, duree INT NOT NULL, INDEX IDX_2EB743D77ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, cours_id INT NOT NULL, instructeur_id INT NOT NULL, apprenant_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, temps_max INT DEFAULT NULL, INDEX IDX_A412FA927ECF78B0 (cours_id), INDEX IDX_A412FA9225FCA809 (instructeur_id), INDEX IDX_A412FA92C5697D6D (apprenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, contenu LONGTEXT NOT NULL, est_correcte TINYINT(1) NOT NULL, INDEX IDX_5FB6DEC71E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resultat (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, apprenant_id INT NOT NULL, score INT NOT NULL, INDEX IDX_E7DB5DE2853CD175 (quiz_id), INDEX IDX_E7DB5DE2C5697D6D (apprenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_connexion DATETIME DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, level VARCHAR(100) DEFAULT NULL, interests JSON DEFAULT NULL, biographie LONGTEXT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCFD02F13 FOREIGN KEY (evenement_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C25FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE defis ADD CONSTRAINT FK_9701665B7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5757ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A6171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_participants ADD CONSTRAINT FK_9C7A7A61A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D77ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA927ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9225FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCFD02F13');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C25FCA809');
        $this->addSql('ALTER TABLE defis DROP FOREIGN KEY FK_9701665B7ECF78B0');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5757ECF78B0');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A6171F7E88B');
        $this->addSql('ALTER TABLE event_participants DROP FOREIGN KEY FK_9C7A7A61A76ED395');
        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D77ECF78B0');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA927ECF78B0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9225FCA809');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92C5697D6D');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2853CD175');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2C5697D6D');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE defis');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_participants');
        $this->addSql('DROP TABLE modules');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE resultat');
        $this->addSql('DROP TABLE user');
    }
}
