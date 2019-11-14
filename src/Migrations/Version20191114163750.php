<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191114163750 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season DROP FOREIGN KEY FK_76F93BCF4B6F93BB');
        $this->addSql('DROP INDEX IDX_76F93BCF4B6F93BB ON bundesliga_season');
        $this->addSql('ALTER TABLE bundesliga_season ADD actual_season TINYINT(1) DEFAULT \'0\' NOT NULL, DROP deputy_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_season ADD deputy_id INT DEFAULT NULL, DROP actual_season');
        $this->addSql('ALTER TABLE bundesliga_season ADD CONSTRAINT FK_76F93BCF4B6F93BB FOREIGN KEY (deputy_id) REFERENCES bundesliga_executive (id)');
        $this->addSql('CREATE INDEX IDX_76F93BCF4B6F93BB ON bundesliga_season (deputy_id)');
    }
}
