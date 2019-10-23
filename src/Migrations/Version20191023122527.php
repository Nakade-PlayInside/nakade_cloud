<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191023122527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player DROP FOREIGN KEY FK_ABF383228D3B01A3');
        $this->addSql('CREATE TABLE bundesliga_season_bundesliga_player (bundesliga_season_id INT NOT NULL, bundesliga_player_id INT NOT NULL, INDEX IDX_C6067F9A461B1BB (bundesliga_season_id), INDEX IDX_C6067F9AD34745B5 (bundesliga_player_id), PRIMARY KEY(bundesliga_season_id, bundesliga_player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_season_bundesliga_player ADD CONSTRAINT FK_C6067F9A461B1BB FOREIGN KEY (bundesliga_season_id) REFERENCES bundesliga_season (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_season_bundesliga_player ADD CONSTRAINT FK_C6067F9AD34745B5 FOREIGN KEY (bundesliga_player_id) REFERENCES bundesliga_player (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE bundesliga_team');
        $this->addSql('DROP TABLE bundesliga_team_bundesliga_player');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_team (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, UNIQUE INDEX UNIQ_17B6E9684EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bundesliga_team_bundesliga_player (bundesliga_team_id INT NOT NULL, bundesliga_player_id INT NOT NULL, INDEX IDX_ABF383228D3B01A3 (bundesliga_team_id), INDEX IDX_ABF38322D34745B5 (bundesliga_player_id), PRIMARY KEY(bundesliga_team_id, bundesliga_player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bundesliga_team ADD CONSTRAINT FK_17B6E9684EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player ADD CONSTRAINT FK_ABF383228D3B01A3 FOREIGN KEY (bundesliga_team_id) REFERENCES bundesliga_team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_team_bundesliga_player ADD CONSTRAINT FK_ABF38322D34745B5 FOREIGN KEY (bundesliga_player_id) REFERENCES bundesliga_player (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE bundesliga_season_bundesliga_player');
    }
}
