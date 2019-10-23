<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191023132655 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_results ADD home_id INT NOT NULL, ADD away_id INT NOT NULL, DROP home, DROP away');
        $this->addSql('ALTER TABLE bundesliga_results ADD CONSTRAINT FK_3BF43D1928CDC89C FOREIGN KEY (home_id) REFERENCES bundesliga_team (id)');
        $this->addSql('ALTER TABLE bundesliga_results ADD CONSTRAINT FK_3BF43D198DEF089F FOREIGN KEY (away_id) REFERENCES bundesliga_team (id)');
        $this->addSql('CREATE INDEX IDX_3BF43D1928CDC89C ON bundesliga_results (home_id)');
        $this->addSql('CREATE INDEX IDX_3BF43D198DEF089F ON bundesliga_results (away_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_results DROP FOREIGN KEY FK_3BF43D1928CDC89C');
        $this->addSql('ALTER TABLE bundesliga_results DROP FOREIGN KEY FK_3BF43D198DEF089F');
        $this->addSql('DROP INDEX IDX_3BF43D1928CDC89C ON bundesliga_results');
        $this->addSql('DROP INDEX IDX_3BF43D198DEF089F ON bundesliga_results');
        $this->addSql('ALTER TABLE bundesliga_results ADD home VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD away VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP home_id, DROP away_id');
    }
}
