<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191023124737 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_results ADD home VARCHAR(255) NOT NULL, ADD away VARCHAR(255) NOT NULL, ADD points_away SMALLINT NOT NULL, ADD points_home SMALLINT NOT NULL, ADD board_points_away SMALLINT DEFAULT NULL, ADD board_points_home SMALLINT DEFAULT NULL, DROP home_team, DROP away_team, DROP points_away_team, DROP points_home_team, DROP board_points_away_team, DROP board_points_home_team, CHANGE details_id details_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_results ADD home_team VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD away_team VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD points_away_team SMALLINT NOT NULL, ADD points_home_team SMALLINT NOT NULL, ADD board_points_away_team SMALLINT DEFAULT NULL, ADD board_points_home_team SMALLINT DEFAULT NULL, DROP home, DROP away, DROP points_away, DROP points_home, DROP board_points_away, DROP board_points_home, CHANGE details_id details_id INT NOT NULL');
    }
}
