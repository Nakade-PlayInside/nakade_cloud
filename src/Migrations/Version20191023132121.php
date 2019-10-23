<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191023132121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bundesliga_results DROP FOREIGN KEY FK_3BF43D19BB1A0722');
        $this->addSql('DROP TABLE bundesliga_details');
        $this->addSql('ALTER TABLE bundesliga_match ADD results_id INT NOT NULL, CHANGE result points SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_match ADD CONSTRAINT FK_B4E977D58A30AB9 FOREIGN KEY (results_id) REFERENCES bundesliga_results (id)');
        $this->addSql('CREATE INDEX IDX_B4E977D58A30AB9 ON bundesliga_match (results_id)');
        $this->addSql('DROP INDEX UNIQ_3BF43D19BB1A0722 ON bundesliga_results');
        $this->addSql('ALTER TABLE bundesliga_results DROP details_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bundesliga_details (id INT AUTO_INCREMENT NOT NULL, first_board_id INT NOT NULL, second_board_id INT NOT NULL, third_board_id INT NOT NULL, fourth_board_id INT NOT NULL, opponent_team VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_D671D287D4F808E0 (second_board_id), UNIQUE INDEX UNIQ_D671D287A11A1A6B (fourth_board_id), UNIQUE INDEX UNIQ_D671D28792145340 (third_board_id), UNIQUE INDEX UNIQ_D671D28753F465DE (first_board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D28753F465DE FOREIGN KEY (first_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D28792145340 FOREIGN KEY (third_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D287A11A1A6B FOREIGN KEY (fourth_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_details ADD CONSTRAINT FK_D671D287D4F808E0 FOREIGN KEY (second_board_id) REFERENCES bundesliga_match (id)');
        $this->addSql('ALTER TABLE bundesliga_match DROP FOREIGN KEY FK_B4E977D58A30AB9');
        $this->addSql('DROP INDEX IDX_B4E977D58A30AB9 ON bundesliga_match');
        $this->addSql('ALTER TABLE bundesliga_match DROP results_id, CHANGE points result SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE bundesliga_results ADD details_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bundesliga_results ADD CONSTRAINT FK_3BF43D19BB1A0722 FOREIGN KEY (details_id) REFERENCES bundesliga_details (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BF43D19BB1A0722 ON bundesliga_results (details_id)');
    }
}
