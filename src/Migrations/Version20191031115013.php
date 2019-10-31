<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191031115013 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_relegation (id INT AUTO_INCREMENT NOT NULL, home_id INT NOT NULL, away_id INT NOT NULL, season_id INT NOT NULL, board_points_away SMALLINT DEFAULT NULL, board_points_home SMALLINT DEFAULT NULL, played_at DATETIME DEFAULT NULL, INDEX IDX_AEB29B3428CDC89C (home_id), INDEX IDX_AEB29B348DEF089F (away_id), INDEX IDX_AEB29B344EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_relegation_match (id INT AUTO_INCREMENT NOT NULL, results_id INT DEFAULT NULL, player_id INT NOT NULL, season_id INT NOT NULL, opponent_id INT NOT NULL, opponent_team_id INT NOT NULL, board SMALLINT NOT NULL, color VARCHAR(10) NOT NULL, points SMALLINT DEFAULT NULL, INDEX IDX_ADC38C588A30AB9 (results_id), INDEX IDX_ADC38C5899E6F5DF (player_id), INDEX IDX_ADC38C584EC001D1 (season_id), INDEX IDX_ADC38C587F656CDC (opponent_id), INDEX IDX_ADC38C58242702A6 (opponent_team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_relegation ADD CONSTRAINT FK_AEB29B3428CDC89C FOREIGN KEY (home_id) REFERENCES bundesliga_team (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation ADD CONSTRAINT FK_AEB29B348DEF089F FOREIGN KEY (away_id) REFERENCES bundesliga_team (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation ADD CONSTRAINT FK_AEB29B344EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C588A30AB9 FOREIGN KEY (results_id) REFERENCES bundesliga_relegation (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C5899E6F5DF FOREIGN KEY (player_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C584EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C587F656CDC FOREIGN KEY (opponent_id) REFERENCES bundesliga_opponent (id)');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD CONSTRAINT FK_ADC38C58242702A6 FOREIGN KEY (opponent_team_id) REFERENCES bundesliga_team (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_relegation_match DROP FOREIGN KEY FK_ADC38C588A30AB9');
        $this->addSql('DROP TABLE bundesliga_relegation');
        $this->addSql('DROP TABLE bundesliga_relegation_match');
    }
}
