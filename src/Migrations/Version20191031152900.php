<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191031152900 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season DROP FOREIGN KEY FK_76F93BCF6442B939');
        $this->addSql('CREATE TABLE bundesliga_lineup (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, position1_id INT NOT NULL, position2_id INT NOT NULL, position3_id INT NOT NULL, position4_id INT NOT NULL, position5_id INT DEFAULT NULL, position6_id INT DEFAULT NULL, position7_id INT DEFAULT NULL, position8_id INT DEFAULT NULL, position9_id INT DEFAULT NULL, position10_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4B636EAC4EC001D1 (season_id), INDEX IDX_4B636EAC490E4D18 (position1_id), INDEX IDX_4B636EAC5BBBE2F6 (position2_id), INDEX IDX_4B636EACE3078593 (position3_id), INDEX IDX_4B636EAC7ED0BD2A (position4_id), INDEX IDX_4B636EACC66CDA4F (position5_id), INDEX IDX_4B636EACD4D975A1 (position6_id), INDEX IDX_4B636EAC6C6512C4 (position7_id), INDEX IDX_4B636EAC34060292 (position8_id), INDEX IDX_4B636EAC8CBA65F7 (position9_id), INDEX IDX_4B636EAC7DF5DCED (position10_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC4EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC490E4D18 FOREIGN KEY (position1_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC5BBBE2F6 FOREIGN KEY (position2_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EACE3078593 FOREIGN KEY (position3_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC7ED0BD2A FOREIGN KEY (position4_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EACC66CDA4F FOREIGN KEY (position5_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EACD4D975A1 FOREIGN KEY (position6_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC6C6512C4 FOREIGN KEY (position7_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC34060292 FOREIGN KEY (position8_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC8CBA65F7 FOREIGN KEY (position9_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_lineup ADD CONSTRAINT FK_4B636EAC7DF5DCED FOREIGN KEY (position10_id) REFERENCES bundesliga_player (id)');
        $this->addSql('DROP TABLE bundesliga_team_lineup');
        $this->addSql('DROP INDEX UNIQ_76F93BCF6442B939 ON bundesliga_season');
        $this->addSql('ALTER TABLE bundesliga_season CHANGE team_lineup_id lineup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_season ADD CONSTRAINT FK_76F93BCFD347A7DE FOREIGN KEY (lineup_id) REFERENCES bundesliga_lineup (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76F93BCFD347A7DE ON bundesliga_season (lineup_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season DROP FOREIGN KEY FK_76F93BCFD347A7DE');
        $this->addSql('CREATE TABLE bundesliga_team_lineup (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, position1_id INT NOT NULL, position2_id INT NOT NULL, position3_id INT NOT NULL, position4_id INT NOT NULL, position5_id INT DEFAULT NULL, position6_id INT DEFAULT NULL, position7_id INT DEFAULT NULL, position8_id INT DEFAULT NULL, position9_id INT DEFAULT NULL, position10_id INT DEFAULT NULL, INDEX IDX_EF1633135BBBE2F6 (position2_id), INDEX IDX_EF1633137ED0BD2A (position4_id), INDEX IDX_EF163313D4D975A1 (position6_id), INDEX IDX_EF16331334060292 (position8_id), INDEX IDX_EF1633137DF5DCED (position10_id), UNIQUE INDEX UNIQ_EF1633134EC001D1 (season_id), INDEX IDX_EF163313490E4D18 (position1_id), INDEX IDX_EF163313E3078593 (position3_id), INDEX IDX_EF163313C66CDA4F (position5_id), INDEX IDX_EF1633136C6512C4 (position7_id), INDEX IDX_EF1633138CBA65F7 (position9_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF16331334060292 FOREIGN KEY (position8_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF163313490E4D18 FOREIGN KEY (position1_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF1633134EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF1633135BBBE2F6 FOREIGN KEY (position2_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF1633136C6512C4 FOREIGN KEY (position7_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF1633137DF5DCED FOREIGN KEY (position10_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF1633137ED0BD2A FOREIGN KEY (position4_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF1633138CBA65F7 FOREIGN KEY (position9_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF163313C66CDA4F FOREIGN KEY (position5_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF163313D4D975A1 FOREIGN KEY (position6_id) REFERENCES bundesliga_player (id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup ADD CONSTRAINT FK_EF163313E3078593 FOREIGN KEY (position3_id) REFERENCES bundesliga_player (id)');
        $this->addSql('DROP TABLE bundesliga_lineup');
        $this->addSql('DROP INDEX UNIQ_76F93BCFD347A7DE ON bundesliga_season');
        $this->addSql('ALTER TABLE bundesliga_season CHANGE lineup_id team_lineup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_season ADD CONSTRAINT FK_76F93BCF6442B939 FOREIGN KEY (team_lineup_id) REFERENCES bundesliga_team_lineup (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76F93BCF6442B939 ON bundesliga_season (team_lineup_id)');
    }
}
