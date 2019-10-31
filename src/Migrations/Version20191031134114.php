<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191031134114 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season ADD team_lineup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_season ADD CONSTRAINT FK_76F93BCF6442B939 FOREIGN KEY (team_lineup_id) REFERENCES bundesliga_team_lineup (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76F93BCF6442B939 ON bundesliga_season (team_lineup_id)');
        $this->addSql('ALTER TABLE bundesliga_team_lineup DROP INDEX IDX_EF1633134EC001D1, ADD UNIQUE INDEX UNIQ_EF1633134EC001D1 (season_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season DROP FOREIGN KEY FK_76F93BCF6442B939');
        $this->addSql('DROP INDEX UNIQ_76F93BCF6442B939 ON bundesliga_season');
        $this->addSql('ALTER TABLE bundesliga_season DROP team_lineup_id');
        $this->addSql('ALTER TABLE bundesliga_team_lineup DROP INDEX UNIQ_EF1633134EC001D1, ADD INDEX IDX_EF1633134EC001D1 (season_id)');
    }
}
