<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191023095409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_details (id INT AUTO_INCREMENT NOT NULL, first_board_id INT NOT NULL, second_board_id INT NOT NULL, third_board_id INT NOT NULL, fourth_board_id INT NOT NULL, opponent_team VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D671D28753F465DE (first_board_id), UNIQUE INDEX UNIQ_D671D287D4F808E0 (second_board_id), UNIQUE INDEX UNIQ_D671D28792145340 (third_board_id), UNIQUE INDEX UNIQ_D671D287A11A1A6B (fourth_board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_season (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, start_at DATE DEFAULT NULL, end_at DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_team (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, UNIQUE INDEX UNIQ_17B6E9684EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_team_bundesliga_player (bundesliga_team_id INT NOT NULL, bundesliga_player_id INT NOT NULL, INDEX IDX_ABF383228D3B01A3 (bundesliga_team_id), INDEX IDX_ABF38322D34745B5 (bundesliga_player_id), PRIMARY KEY(bundesliga_team_id, bundesliga_player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_match (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, season_id INT NOT NULL, opponent_id INT NOT NULL, color VARCHAR(10) NOT NULL, result SMALLINT NOT NULL, INDEX IDX_B4E977D599E6F5DF (player_id), INDEX IDX_B4E977D54EC001D1 (season_id), INDEX IDX_B4E977D57F656CDC (opponent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_results (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, details_id INT NOT NULL, match_day SMALLINT NOT NULL, home_team VARCHAR(255) NOT NULL, away_team VARCHAR(255) NOT NULL, points_away_team SMALLINT NOT NULL, points_home_team SMALLINT NOT NULL, board_points_away_team SMALLINT DEFAULT NULL, board_points_home_team SMALLINT DEFAULT NULL, played_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3BF43D194EC001D1 (season_id), UNIQUE INDEX UNIQ_3BF43D19BB1A0722 (details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_opponent (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bundesliga_player (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_day DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D28753F465DE FOREIGN KEY (first_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D287D4F808E0 FOREIGN KEY (second_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D28792145340 FOREIGN KEY (third_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D287A11A1A6B FOREIGN KEY (fourth_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_team ADD CONSTRAINT FK_17B6E9684EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player ADD CONSTRAINT FK_ABF383228D3B01A3 FOREIGN KEY (bundesliga_team_id) REFERENCES bundesliga_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player ADD CONSTRAINT FK_ABF38322D34745B5 FOREIGN KEY (bundesliga_player_id) REFERENCES bundesliga_player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D599E6F5DF FOREIGN KEY (player_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D54EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D57F656CDC FOREIGN KEY (opponent_id) REFERENCES bundesliga_opponent (id)');
        $this->addSql('ALTER TABLE bundesliga_results ADD CONSTRAINT FK_3BF43D194EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_results ADD CONSTRAINT FK_3BF43D19BB1A0722 FOREIGN KEY (details_id) REFERENCES bundesliga_details (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_results DROP FOREIGN KEY FK_3BF43D19BB1A0722');
        $this->addSql('ALTER TABLE bundesliga_team DROP FOREIGN KEY FK_17B6E9684EC001D1');
        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D54EC001D1');
        $this->addSql('ALTER TABLE bundesliga_results DROP FOREIGN KEY FK_3BF43D194EC001D1');
        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player DROP FOREIGN KEY FK_ABF383228D3B01A3');
        $this->addSql('ALTER TABLE bundesliga_details DROP FOREIGN KEY FK_D671D28753F465DE');
        $this->addSql('ALTER TABLE bundesliga_details DROP FOREIGN KEY FK_D671D287D4F808E0');
        $this->addSql('ALTER TABLE bundesliga_details DROP FOREIGN KEY FK_D671D28792145340');
        $this->addSql('ALTER TABLE bundesliga_details DROP FOREIGN KEY FK_D671D287A11A1A6B');
        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D57F656CDC');
        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player DROP FOREIGN KEY FK_ABF38322D34745B5');
        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D599E6F5DF');
        $this->addSql('DROP TABLE bundesliga_details');
        $this->addSql('DROP TABLE bundesliga_season');
        $this->addSql('DROP TABLE bundesliga_team');
        $this->addSql('DROP TABLE bundesliga_team_bundesliga_player');
        $this->addSql('DROP TABLE bundesliga_match');
        $this->addSql('DROP TABLE bundesliga_results');
        $this->addSql('DROP TABLE bundesliga_opponent');
        $this->addSql('DROP TABLE bundesliga_player');
    }
}
