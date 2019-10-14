<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191014133122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE contact_reply (id INT AUTO_INCREMENT NOT NULL, recipient_id INT NOT NULL, editor_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D082D6EFE92F8F78 (recipient_id), INDEX IDX_D082D6EF6995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_reply ADD CONSTRAINT FK_D082D6EFE92F8F78 FOREIGN KEY (recipient_id) REFERENCES contact_mail (id)');
        $this->addSql('ALTER TABLE contact_reply ADD CONSTRAINT FK_D082D6EF6995AC4C FOREIGN KEY (editor_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE contact_response');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE contact_response (id INT AUTO_INCREMENT NOT NULL, recipient_id INT NOT NULL, editor_id INT NOT NULL, message LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A45E976CE92F8F78 (recipient_id), INDEX IDX_A45E976C6995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE contact_response ADD CONSTRAINT FK_A45E976C6995AC4C FOREIGN KEY (editor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact_response ADD CONSTRAINT FK_A45E976CE92F8F78 FOREIGN KEY (recipient_id) REFERENCES contact_mail (id)');
        $this->addSql('DROP TABLE contact_reply');
    }
}
