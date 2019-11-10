<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191110121152 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_match CHANGE player_id player_id INT DEFAULT NULL, CHANGE opponent_id opponent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_relegation_match CHANGE player_id player_id INT DEFAULT NULL, CHANGE opponent_id opponent_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_match CHANGE player_id player_id INT NOT NULL, CHANGE opponent_id opponent_id INT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_relegation_match CHANGE player_id player_id INT NOT NULL, CHANGE opponent_id opponent_id INT NOT NULL');
    }
}
