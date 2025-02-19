<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217231655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD prix NUMERIC(10, 2) NOT NULL, ADD difficulte ENUM(\'Beginner\', \'Intermediate\', \'Advanced\') NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE defis ADD cours_id INT NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD points_recompense INT NOT NULL, ADD badge_recompense VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE defis ADD CONSTRAINT FK_9701665B7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_9701665B7ECF78B0 ON defis (cours_id)');
        $this->addSql('ALTER TABLE modules ADD cours_id INT NOT NULL, ADD titre VARCHAR(255) NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD duree INT NOT NULL');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D77ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_2EB743D77ECF78B0 ON modules (cours_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE last_connexion last_connexion DATETIME DEFAULT NULL, CHANGE level level VARCHAR(100) DEFAULT NULL, CHANGE nom nom VARCHAR(100) DEFAULT NULL, CHANGE prenom prenom VARCHAR(100) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE interests interests JSON DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP prix, DROP difficulte, CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE defis DROP FOREIGN KEY FK_9701665B7ECF78B0');
        $this->addSql('DROP INDEX IDX_9701665B7ECF78B0 ON defis');
        $this->addSql('ALTER TABLE defis DROP cours_id, DROP titre, DROP description, DROP points_recompense, DROP badge_recompense');
        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D77ECF78B0');
        $this->addSql('DROP INDEX IDX_2EB743D77ECF78B0 ON modules');
        $this->addSql('ALTER TABLE modules DROP cours_id, DROP titre, DROP contenu, DROP duree');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE last_connexion last_connexion DATETIME DEFAULT \'NULL\', CHANGE level level VARCHAR(100) DEFAULT \'NULL\', CHANGE nom nom VARCHAR(100) DEFAULT \'NULL\', CHANGE prenom prenom VARCHAR(100) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\', CHANGE interests interests LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE photo photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}
