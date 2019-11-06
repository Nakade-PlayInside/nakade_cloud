<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191105235509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bundesliga_season_bundesliga_player');
        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D5242702A6');
        $this->addSql('DROP INDEX IDX_B4E977D5242702A6 ON bundesliga_match');
        $this->addSql('ALTER TABLE bundesliga_match DROP opponent_team_id');
        $this->addSql('ALTER TABLE bundesliga_relegation_match DROP FOREIGN KEY FK_ADC38C58242702A6');
        $this->addSql('DROP INDEX IDX_ADC38C58242702A6 ON bundesliga_relegation_match');
        $this->addSql('ALTER TABLE bundesliga_relegation_match DROP opponent_team_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_season_bundesliga_player (bundesliga_season_id INT NOT NULL, bundesliga_player_id INT NOT NULL, INDEX IDX_C6067F9A461B1BB (bundesliga_season_id), INDEX IDX_C6067F9AD34745B5 (bundesliga_player_id), PRIMARY KEY(bundesliga_season_id, bundesliga_player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bundesliga_season_bundesliga_player ADD CONSTRAINT FK_C6067F9A461B1BB FOREIGN KEY (bundesliga_season_id) REFERENCES bundesliga_season (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_season_bundesliga_player ADD CONSTRAINT FK_C6067F9AD34745B5 FOREIGN KEY (bundesliga_player_id) REFERENCES bundesliga_player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_match ADD opponent_team_id INT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D5242702A6 FOREIGN KEY (opponent_team_id) REFERENCES bundesliga_team (id)');
        $this->addSql('CREATE INDEX IDX_B4E977D5242702A6 ON bundesliga_match (opponent_team_id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD opponent_team_id INT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C58242702A6 FOREIGN KEY (opponent_team_id) REFERENCES bundesliga_team (id)');
        $this->addSql('CREATE INDEX IDX_ADC38C58242702A6 ON bundesliga_relegation_match (opponent_team_id)');
    }
}
