<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213020052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apprenants (id INT NOT NULL, level VARCHAR(100) DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, interests JSON DEFAULT NULL, completed_courses INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instructeurs (id INT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, biographie LONGTEXT DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apprenants ADD CONSTRAINT FK_C71C2982BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE instructeurs ADD CONSTRAINT FK_246D4568BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_cours DROP FOREIGN KEY FK_75FDF501C5697D6D');
        $this->addSql('ALTER TABLE student_cours ADD CONSTRAINT FK_75FDF501C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C25FCA809');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C25FCA809 FOREIGN KEY (instructeur_id) REFERENCES instructeurs (id)');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575C5697D6D');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575C5697D6D FOREIGN KEY (apprenant_id) REFERENCES apprenants (id)');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP level, DROP nom, DROP prenom, DROP telephone, DROP interests, DROP completed_courses, DROP biographie, DROP photo, CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_cours DROP FOREIGN KEY FK_75FDF501C5697D6D');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575C5697D6D');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C25FCA809');
        $this->addSql('ALTER TABLE apprenants DROP FOREIGN KEY FK_C71C2982BF396750');
        $this->addSql('ALTER TABLE instructeurs DROP FOREIGN KEY FK_246D4568BF396750');
        $this->addSql('DROP TABLE apprenants');
        $this->addSql('DROP TABLE instructeurs');
        $this->addSql('ALTER TABLE commentaire CHANGE date_actuelle date_actuelle DATETIME DEFAULT \'current_timestamp()\' NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C25FCA809');
        $this->addSql('ALTER TABLE cours CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C25FCA809 FOREIGN KEY (instructeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575C5697D6D');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\', CHANGE type type VARCHAR(100) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE student_cours DROP FOREIGN KEY FK_75FDF501C5697D6D');
        $this->addSql('ALTER TABLE student_cours ADD CONSTRAINT FK_75FDF501C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD level VARCHAR(100) DEFAULT \'NULL\', ADD nom VARCHAR(100) DEFAULT \'NULL\', ADD prenom VARCHAR(100) DEFAULT \'NULL\', ADD telephone VARCHAR(20) DEFAULT \'NULL\', ADD interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, ADD completed_courses INT DEFAULT NULL, ADD biographie LONGTEXT DEFAULT NULL, ADD photo VARCHAR(255) DEFAULT \'NULL\', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\'');
    }
}
