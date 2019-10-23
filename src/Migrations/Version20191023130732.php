<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191023130732 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_match ADD opponent_team_id INT NOT NULL, ADD board SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D5242702A6 FOREIGN KEY (opponent_team_id) REFERENCES bundesliga_team (id)');
        $this->addSql('CREATE INDEX IDX_B4E977D5242702A6 ON bundesliga_match (opponent_team_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D5242702A6');
        $this->addSql('DROP TABLE bundesliga_team');
        $this->addSql('DROP INDEX IDX_B4E977D5242702A6 ON bundesliga_match');
        $this->addSql('ALTER TABLE bundesliga_match DROP opponent_team_id, DROP board');
    }
}
