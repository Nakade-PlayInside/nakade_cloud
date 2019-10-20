<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191020110422 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, feature_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C60E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('DROP TABLE feature_comment');
        $this->addSql('ALTER TABLE feature ADD tracker_type VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(20) NOT NULL, CHANGE priority priority SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feature_comment (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, author_id INT NOT NULL, message LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A563C965F675F31B (author_id), INDEX IDX_A563C965727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE feature_comment ADD CONSTRAINT FK_A563C965727ACA70 FOREIGN KEY (parent_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE feature_comment ADD CONSTRAINT FK_A563C965F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE feature DROP tracker_type, CHANGE status status VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE priority priority SMALLINT NOT NULL');
    }
}
