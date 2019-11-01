<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191031154555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season DROP FOREIGN KEY FK_76F93BCFD347A7DE');
        $this->addSql('DROP INDEX UNIQ_76F93BCFD347A7DE ON bundesliga_season');
        $this->addSql('ALTER TABLE bundesliga_season DROP lineup_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season ADD lineup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_season ADD CONSTRAINT FK_76F93BCFD347A7DE FOREIGN KEY (lineup_id) REFERENCES bundesliga_lineup (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76F93BCFD347A7DE ON bundesliga_season (lineup_id)');
    }
}
