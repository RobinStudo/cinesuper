<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200116160206 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D39357C6E5');
        $this->addSql('DROP INDEX IDX_161498D39357C6E5 ON card');
        $this->addSql('ALTER TABLE card DROP gifts_id');
        $this->addSql('ALTER TABLE gift DROP FOREIGN KEY FK_A47C990D4ACC9A20');
        $this->addSql('DROP INDEX IDX_A47C990D4ACC9A20 ON gift');
        $this->addSql('ALTER TABLE gift DROP card_id');
        $this->addSql('ALTER TABLE gift_card ADD gifts_id INT NOT NULL, ADD cards_id INT NOT NULL');
        $this->addSql('ALTER TABLE gift_card ADD CONSTRAINT FK_E4696A8E9357C6E5 FOREIGN KEY (gifts_id) REFERENCES gift (id)');
        $this->addSql('ALTER TABLE gift_card ADD CONSTRAINT FK_E4696A8EDC555177 FOREIGN KEY (cards_id) REFERENCES card (id)');
        $this->addSql('CREATE INDEX IDX_E4696A8E9357C6E5 ON gift_card (gifts_id)');
        $this->addSql('CREATE INDEX IDX_E4696A8EDC555177 ON gift_card (cards_id)');
        $this->addSql('ALTER TABLE picture CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE picture_id picture_id INT DEFAULT NULL, CHANGE token token VARCHAR(255) DEFAULT NULL, CHANGE token_expire token_expire DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card ADD gifts_id INT NOT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D39357C6E5 FOREIGN KEY (gifts_id) REFERENCES gift_card (id)');
        $this->addSql('CREATE INDEX IDX_161498D39357C6E5 ON card (gifts_id)');
        $this->addSql('ALTER TABLE gift ADD card_id INT NOT NULL');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D4ACC9A20 FOREIGN KEY (card_id) REFERENCES gift_card (id)');
        $this->addSql('CREATE INDEX IDX_A47C990D4ACC9A20 ON gift (card_id)');
        $this->addSql('ALTER TABLE gift_card DROP FOREIGN KEY FK_E4696A8E9357C6E5');
        $this->addSql('ALTER TABLE gift_card DROP FOREIGN KEY FK_E4696A8EDC555177');
        $this->addSql('DROP INDEX IDX_E4696A8E9357C6E5 ON gift_card');
        $this->addSql('DROP INDEX IDX_E4696A8EDC555177 ON gift_card');
        $this->addSql('ALTER TABLE gift_card DROP gifts_id, DROP cards_id');
        $this->addSql('ALTER TABLE picture CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE picture_id picture_id INT DEFAULT NULL, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token_expire token_expire DATETIME DEFAULT \'NULL\'');
    }
}
