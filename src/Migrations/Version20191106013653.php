<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106013653 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_penalty DROP FOREIGN KEY FK_BB556D54EC001D1');
        $this->addSql('ALTER TABLE bundesliga_penalty CHANGE season_id season_id INT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_penalty ADD CONSTRAINT FK_BB556D54EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_season (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_penalty DROP FOREIGN KEY FK_BB556D54EC001D1');
        $this->addSql('ALTER TABLE bundesliga_penalty CHANGE season_id season_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_penalty ADD CONSTRAINT FK_BB556D54EC001D1 FOREIGN KEY (season_id) REFERENCES bundesliga_team (id)');
    }
}
