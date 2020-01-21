<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200116180414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, card_id INT NOT NULL, gift_type_id INT NOT NULL, serial VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_A47C990DD374C9DC (serial), INDEX IDX_A47C990D4ACC9A20 (card_id), INDEX IDX_A47C990DA916BF3C (gift_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, expired_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DA916BF3C FOREIGN KEY (gift_type_id) REFERENCES gift_type (id)');
        $this->addSql('DROP TABLE voucher');
        $this->addSql('ALTER TABLE event ADD description LONGTEXT DEFAULT NULL, ADD multiplicateur NUMERIC(10, 0) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990DA916BF3C');
        $this->addSql('CREATE TABLE voucher (id INT AUTO_INCREMENT NOT NULL, card_id INT NOT NULL, serial VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, expired_at DATETIME NOT NULL, INDEX IDX_1392A5D84ACC9A20 (card_id), UNIQUE INDEX UNIQ_1392A5D8D374C9DC (serial), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE voucher ADD CONSTRAINT FK_1392A5D84ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE gift_type');
        $this->addSql('ALTER TABLE event DROP description, DROP multiplicateur');
    }
}
