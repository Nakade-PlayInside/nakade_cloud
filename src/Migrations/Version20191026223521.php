<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191026223521 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_executive (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, function VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_season ADD executive_id INT DEFAULT NULL, ADD league VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_season ADD CONSTRAINT FK_76F93BCFEDE327B6 FOREIGN KEY (executive_id) REFERENCES bundesliga_executive (id)');
        $this->addSql('CREATE INDEX IDX_76F93BCFEDE327B6 ON bundesliga_season (executive_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season DROP FOREIGN KEY FK_76F93BCFEDE327B6');
        $this->addSql('DROP TABLE bundesliga_executive');
        $this->addSql('DROP INDEX IDX_76F93BCFEDE327B6 ON bundesliga_season');
        $this->addSql('ALTER TABLE bundesliga_season DROP executive_id, DROP league');
    }
}
