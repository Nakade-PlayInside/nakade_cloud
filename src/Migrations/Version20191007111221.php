<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191007111221 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_40964A485F37A13B ON news_reader');
        $this->addSql('ALTER TABLE news_reader ADD unsubscribe_token VARCHAR(255) NOT NULL, CHANGE token subscribe_token VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40964A487782E8E8 ON news_reader (subscribe_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40964A48E0674361 ON news_reader (unsubscribe_token)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_40964A487782E8E8 ON news_reader');
        $this->addSql('DROP INDEX UNIQ_40964A48E0674361 ON news_reader');
        $this->addSql('ALTER TABLE news_reader ADD token VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP subscribe_token, DROP unsubscribe_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40964A485F37A13B ON news_reader (token)');
    }
}
