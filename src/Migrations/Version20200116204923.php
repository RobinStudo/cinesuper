<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200116204923 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event ADD duration INT NOT NULL, DROP end_at, CHANGE name name VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE gift ADD expired_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE gift_type ADD duration INT NOT NULL, ADD fidelity_cost INT NOT NULL, DROP expired_at');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event ADD end_at DATETIME NOT NULL, DROP duration, CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE gift DROP expired_at');
        $this->addSql('ALTER TABLE gift_type ADD expired_at DATETIME NOT NULL, DROP duration, DROP fidelity_cost');
    }
}
