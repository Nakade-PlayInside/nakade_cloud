<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191020140419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bug_comment (id INT AUTO_INCREMENT NOT NULL, bug_report_id INT NOT NULL, author_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_CE4350DC41193163 (bug_report_id), INDEX IDX_CE4350DCF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bug_report (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, priority SMALLINT NOT NULL, closed_at DATETIME DEFAULT NULL, message LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F6F2DC7AF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bug_comment ADD CONSTRAINT FK_CE4350DC41193163 FOREIGN KEY (bug_report_id) REFERENCES bug_report (id)');
        $this->addSql('ALTER TABLE bug_comment ADD CONSTRAINT FK_CE4350DCF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7AF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE feature_comment DROP FOREIGN KEY FK_A563C965727ACA70');
        $this->addSql('DROP INDEX IDX_A563C965727ACA70 ON feature_comment');
        $this->addSql('ALTER TABLE feature_comment CHANGE parent_id feature_id INT NOT NULL');
        $this->addSql('ALTER TABLE feature_comment ADD CONSTRAINT FK_A563C96560E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('CREATE INDEX IDX_A563C96560E4B879 ON feature_comment (feature_id)');
        $this->addSql('ALTER TABLE feature ADD title VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bug_comment DROP FOREIGN KEY FK_CE4350DC41193163');
        $this->addSql('DROP TABLE bug_comment');
        $this->addSql('DROP TABLE bug_report');
        $this->addSql('ALTER TABLE feature DROP title, CHANGE status status VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE feature_comment DROP FOREIGN KEY FK_A563C96560E4B879');
        $this->addSql('DROP INDEX IDX_A563C96560E4B879 ON feature_comment');
        $this->addSql('ALTER TABLE feature_comment CHANGE feature_id parent_id INT NOT NULL');
        $this->addSql('ALTER TABLE feature_comment ADD CONSTRAINT FK_A563C965727ACA70 FOREIGN KEY (parent_id) REFERENCES feature (id)');
        $this->addSql('CREATE INDEX IDX_A563C965727ACA70 ON feature_comment (parent_id)');
    }
}
