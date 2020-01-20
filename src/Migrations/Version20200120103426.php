<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200120103426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_table ADD bundesliga_season_id INT DEFAULT NULL, ADD bundesliga_team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_table ADD CONSTRAINT FK_389B3D96461B1BB FOREIGN KEY (bundesliga_season_id) REFERENCES bundesliga_season (id)');
        $this->addSql('ALTER TABLE bundesliga_table ADD CONSTRAINT FK_389B3D968D3B01A3 FOREIGN KEY (bundesliga_team_id) REFERENCES bundesliga_team (id)');
        $this->addSql('CREATE INDEX IDX_389B3D96461B1BB ON bundesliga_table (bundesliga_season_id)');
        $this->addSql('CREATE INDEX IDX_389B3D968D3B01A3 ON bundesliga_table (bundesliga_team_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_table DROP FOREIGN KEY FK_389B3D96461B1BB');
        $this->addSql('ALTER TABLE bundesliga_table DROP FOREIGN KEY FK_389B3D968D3B01A3');
        $this->addSql('DROP INDEX IDX_389B3D96461B1BB ON bundesliga_table');
        $this->addSql('DROP INDEX IDX_389B3D968D3B01A3 ON bundesliga_table');
        $this->addSql('ALTER TABLE bundesliga_table DROP bundesliga_season_id, DROP bundesliga_team_id');
    }
}
