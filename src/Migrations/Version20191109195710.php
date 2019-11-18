<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191109195710 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, sexe VARCHAR(255) DEFAULT NULL, quit VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E9652127D6 FOREIGN KEY (emetteur) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE Publications ADD CONSTRAINT FK_2A49E10C8D93D649 FOREIGN KEY (user) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE Commentaire ADD CONSTRAINT FK_E16CE76B32783AF4 FOREIGN KEY (publications) REFERENCES Publications (id)');
        $this->addSql('ALTER TABLE Commentaire ADD CONSTRAINT FK_E16CE76B8D93D649 FOREIGN KEY (user) REFERENCES utilisateurs (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E9652127D6');
        $this->addSql('ALTER TABLE Publications DROP FOREIGN KEY FK_2A49E10C8D93D649');
        $this->addSql('ALTER TABLE Commentaire DROP FOREIGN KEY FK_E16CE76B8D93D649');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('ALTER TABLE Commentaire DROP FOREIGN KEY FK_E16CE76B32783AF4');
    }
}
