<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404111709 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_389B3D96F0E45BA93EB4C318FF232B31462CE4F5E1EE884E ON bundesliga_table');
        $this->addSql('ALTER TABLE bundesliga_table DROP season, DROP league, DROP team');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B3D96461B1BB8D3B01A3E1EE884E ON bundesliga_table (bundesliga_season_id, bundesliga_team_id, match_day)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_389B3D96461B1BB8D3B01A3E1EE884E ON bundesliga_table');
        $this->addSql('ALTER TABLE bundesliga_table ADD season VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD league VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD team VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B3D96F0E45BA93EB4C318FF232B31462CE4F5E1EE884E ON bundesliga_table (season, league, games, position, match_day)');
    }
}
