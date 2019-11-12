<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191016134032 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE OEUVRE DROP FOREIGN KEY fk_oeuvre_auteur');
        $this->addSql('ALTER TABLE EMPRUNT DROP FOREIGN KEY fk_emprunt_noexemplaire');
        $this->addSql('ALTER TABLE EXEMPLAIRE DROP FOREIGN KEY fk_exemplaire_oeuvre');
        $this->addSql('ALTER TABLE commercants DROP FOREIGN KEY FK_66E5E59CC54C8C93');
        $this->addSql('CREATE TABLE typemigration (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE famille (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE AUTEUR');
        $this->addSql('DROP TABLE EMPRUNT');
        $this->addSql('DROP TABLE EXEMPLAIRE');
        $this->addSql('DROP TABLE OEUVRE');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE commercants');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE typeProduits');
        $this->addSql('DROP TABLE type_produit');
        $this->addSql('DROP TABLE types');
        $this->addSql('ALTER TABLE ADHERENT MODIFY idAdherent INT NOT NULL');
        $this->addSql('ALTER TABLE ADHERENT DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ADHERENT ADD nom VARCHAR(255) NOT NULL, ADD prenom VARCHAR(255) NOT NULL, ADD telephone INT NOT NULL, ADD adresse_mail VARCHAR(255) NOT NULL, ADD date_naissance DATE NOT NULL, ADD droit_image TINYINT(1) NOT NULL, ADD est_verifie TINYINT(1) NOT NULL, DROP nomAdherent, DROP datePaiement, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE idadherent id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE ADHERENT ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE AUTEUR (idAuteur INT AUTO_INCREMENT NOT NULL, nomAuteur VARCHAR(255) DEFAULT NULL COLLATE latin1_swedish_ci, prenomAuteur VARCHAR(255) DEFAULT NULL COLLATE latin1_swedish_ci, PRIMARY KEY(idAuteur)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE EMPRUNT (idAdherent INT DEFAULT NULL, noExemplaire INT DEFAULT NULL, dateEmprunt DATE DEFAULT NULL, dateRendu DATE DEFAULT NULL, INDEX fk_emprunt_noexemplaire (noExemplaire), INDEX fk_emprunt_idAdherent (idAdherent)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE EXEMPLAIRE (noExemplaire INT AUTO_INCREMENT NOT NULL, etat VARCHAR(255) DEFAULT NULL COLLATE latin1_swedish_ci, dateAchat DATE DEFAULT NULL, prix NUMERIC(9, 2) DEFAULT NULL, noOeuvre INT DEFAULT NULL, INDEX fk_exemplaire_oeuvre (noOeuvre), PRIMARY KEY(noExemplaire)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE OEUVRE (noOeuvre INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL COLLATE latin1_swedish_ci, dateParution DATE DEFAULT NULL, idAuteur INT DEFAULT NULL, INDEX fk_oeuvre_auteur (idAuteur), PRIMARY KEY(noOeuvre)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, password VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(60) NOT NULL COLLATE utf8mb4_unicode_ci, is_active TINYINT(1) NOT NULL, roles VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, nom VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ville VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, code_postal VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, adresse VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, token_mail VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commercants (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, date_installation DATE NOT NULL, prix_location DOUBLE PRECISION NOT NULL, INDEX IDX_66E5E59CC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, typeProduit_id INT DEFAULT NULL, nom VARCHAR(250) DEFAULT NULL COLLATE utf8_unicode_ci, prix DOUBLE PRECISION DEFAULT NULL, photo VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE typeProduits (id INT NOT NULL, libelle VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type_produit (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE types (id INT AUTO_INCREMENT NOT NULL, nom_commercant VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE EMPRUNT ADD CONSTRAINT fk_emprunt_idAdherent FOREIGN KEY (idAdherent) REFERENCES ADHERENT (idAdherent) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE EMPRUNT ADD CONSTRAINT fk_emprunt_noexemplaire FOREIGN KEY (noExemplaire) REFERENCES EXEMPLAIRE (noExemplaire) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE EXEMPLAIRE ADD CONSTRAINT fk_exemplaire_oeuvre FOREIGN KEY (noOeuvre) REFERENCES OEUVRE (noOeuvre) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE OEUVRE ADD CONSTRAINT fk_oeuvre_auteur FOREIGN KEY (idAuteur) REFERENCES AUTEUR (idAuteur) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commercants ADD CONSTRAINT FK_66E5E59CC54C8C93 FOREIGN KEY (type_id) REFERENCES types (id)');
        $this->addSql('DROP TABLE typemigration');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE famille');
        $this->addSql('ALTER TABLE adherent MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE adherent DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE adherent ADD nomAdherent VARCHAR(255) DEFAULT NULL COLLATE latin1_swedish_ci, ADD datePaiement DATE DEFAULT NULL, DROP nom, DROP prenom, DROP telephone, DROP adresse_mail, DROP date_naissance, DROP droit_image, DROP est_verifie, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL COLLATE latin1_swedish_ci, CHANGE id idAdherent INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE adherent ADD PRIMARY KEY (idAdherent)');
    }
}
