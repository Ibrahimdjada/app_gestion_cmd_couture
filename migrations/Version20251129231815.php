<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251129231815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, usert_id INT DEFAULT NULL, type_com VARCHAR(255) NOT NULL, dat_com DATE NOT NULL, dat_rec DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, avance DOUBLE PRECISION DEFAULT NULL, reste DOUBLE PRECISION DEFAULT NULL, reliquat DOUBLE PRECISION DEFAULT NULL, statut VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, path_mod JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', path_tissu JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_6EEAA67DA76ED395 (user_id), INDEX IDX_6EEAA67DB03A8386 (created_by_id), INDEX IDX_6EEAA67D896DBBDE (updated_by_id), INDEX IDX_6EEAA67DFA648C84 (usert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesure (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, user_id INT DEFAULT NULL, epaule NUMERIC(4, 2) DEFAULT NULL, poitrine NUMERIC(4, 2) DEFAULT NULL, manche NUMERIC(4, 2) DEFAULT NULL, encolure NUMERIC(4, 2) DEFAULT NULL, poignee NUMERIC(4, 2) DEFAULT NULL, ecart_dos NUMERIC(4, 2) DEFAULT NULL, tour_ventrale NUMERIC(4, 2) DEFAULT NULL, longueur NUMERIC(4, 2) DEFAULT NULL, cuisse NUMERIC(4, 2) DEFAULT NULL, fermeture NUMERIC(4, 2) DEFAULT NULL, ceinture NUMERIC(4, 2) DEFAULT NULL, taille NUMERIC(4, 2) DEFAULT NULL, longueur_pantalon NUMERIC(4, 2) DEFAULT NULL, bas_pantalon NUMERIC(4, 2) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5F1B6E70B03A8386 (created_by_id), INDEX IDX_5F1B6E70896DBBDE (updated_by_id), INDEX IDX_5F1B6E70A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pret (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, usert_id INT DEFAULT NULL, mnt_p DOUBLE PRECISION NOT NULL, dat_p DATE NOT NULL, dat_ech DATE NOT NULL, prd DOUBLE PRECISION NOT NULL, ms DOUBLE PRECISION NOT NULL, stat VARCHAR(255) NOT NULL, reliquat DOUBLE PRECISION DEFAULT NULL, reste DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_52ECE979B03A8386 (created_by_id), INDEX IDX_52ECE979896DBBDE (updated_by_id), INDEX IDX_52ECE979FA648C84 (usert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rdv (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, INDEX IDX_10C31F8682EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, is_client TINYINT(1) NOT NULL, is_tailleur TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFA648C84 FOREIGN KEY (usert_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE979B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE979896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE979FA648C84 FOREIGN KEY (usert_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DB03A8386');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D896DBBDE');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFA648C84');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70B03A8386');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70896DBBDE');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70A76ED395');
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE979B03A8386');
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE979896DBBDE');
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE979FA648C84');
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F8682EA2E54');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE mesure');
        $this->addSql('DROP TABLE pret');
        $this->addSql('DROP TABLE rdv');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
