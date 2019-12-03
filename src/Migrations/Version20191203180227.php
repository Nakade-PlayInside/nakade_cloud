<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191203180227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_relegation ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP created_at');
        $this->addSql('ALTER TABLE bundesliga_match ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_results ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, DROP created_at');
        $this->addSql('ALTER TABLE bundesliga_relegation_match ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_match DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE bundesliga_relegation ADD created_at DATETIME DEFAULT NULL, DROP updated_at');
        $this->addSql('ALTER TABLE bundesliga_relegation_match DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE bundesliga_results ADD created_at DATETIME DEFAULT NULL, DROP updated_at');
    }
}
