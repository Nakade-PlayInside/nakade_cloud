<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120223246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_389B3D96F0E45BA93EB4C318FF232B31462CE4F5 ON bundesliga_table');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B3D96F0E45BA93EB4C318FF232B31462CE4F5C4E0A61F ON bundesliga_table (season, league, games, position, team)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_389B3D96F0E45BA93EB4C318FF232B31462CE4F5C4E0A61F ON bundesliga_table');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B3D96F0E45BA93EB4C318FF232B31462CE4F5 ON bundesliga_table (season, league, games, position)');
    }
}
