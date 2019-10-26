<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191025080344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_season_bundesliga_team (bundesliga_season_id INT NOT NULL, bundesliga_team_id INT NOT NULL, INDEX IDX_590C7521461B1BB (bundesliga_season_id), INDEX IDX_590C75218D3B01A3 (bundesliga_team_id), PRIMARY KEY(bundesliga_season_id, bundesliga_team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bundesliga_season_bundesliga_team ADD CONSTRAINT FK_590C7521461B1BB FOREIGN KEY (bundesliga_season_id) REFERENCES bundesliga_season (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bundesliga_season_bundesliga_team ADD CONSTRAINT FK_590C75218D3B01A3 FOREIGN KEY (bundesliga_team_id) REFERENCES bundesliga_team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bundesliga_season_bundesliga_team');
    }
}
