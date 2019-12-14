<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191214112711 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_sgf (id INT AUTO_INCREMENT NOT NULL, played_at DATETIME DEFAULT NULL, kgs_archives_path VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, is_commented TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_match ADD sgf_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D5902BC16C FOREIGN KEY (sgf_id) REFERENCES bundesliga_sgf (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B4E977D5902BC16C ON bundesliga_match (sgf_id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD sgf_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C58902BC16C FOREIGN KEY (sgf_id) REFERENCES bundesliga_sgf (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ADC38C58902BC16C ON bundesliga_relegation_match (sgf_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D5902BC16C');
        $this->addSql('ALTER TABLE bundesliga_relegation_match DROP FOREIGN KEY FK_ADC38C58902BC16C');
        $this->addSql('DROP TABLE bundesliga_sgf');
        $this->addSql('DROP INDEX UNIQ_B4E977D5902BC16C ON bundesliga_match');
        $this->addSql('ALTER TABLE bundesliga_match DROP sgf_id');
        $this->addSql('DROP INDEX UNIQ_ADC38C58902BC16C ON bundesliga_relegation_match');
        $this->addSql('ALTER TABLE bundesliga_relegation_match DROP sgf_id');
    }
}
