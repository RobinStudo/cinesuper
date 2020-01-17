<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200116155033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, card_id INT NOT NULL, title VARCHAR(100) NOT NULL, nb_fidelity INT NOT NULL, picture VARCHAR(100) NOT NULL, INDEX IDX_A47C990D4ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_card (id INT AUTO_INCREMENT NOT NULL, serial VARCHAR(180) NOT NULL, expired_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D4ACC9A20 FOREIGN KEY (card_id) REFERENCES gift_card (id)');
        $this->addSql('ALTER TABLE card ADD gifts_id INT NOT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D39357C6E5 FOREIGN KEY (gifts_id) REFERENCES gift_card (id)');
        $this->addSql('CREATE INDEX IDX_161498D39357C6E5 ON card (gifts_id)');
        $this->addSql('ALTER TABLE picture CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE picture_id picture_id INT DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE token_expire token_expire DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D39357C6E5');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D4ACC9A20');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE gift_card');
        $this->addSql('DROP INDEX IDX_161498D39357C6E5 ON card');
        $this->addSql('ALTER TABLE card DROP gifts_id');
        $this->addSql('ALTER TABLE picture CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE picture_id picture_id INT DEFAULT NULL, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token_expire token_expire DATETIME DEFAULT \'NULL\'');
    }
}
