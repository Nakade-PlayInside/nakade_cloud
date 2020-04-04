<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404113100 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }

    public function postUp(Schema $schema): void
    {
        /** @var EntityManager $manager */
        $manager = $this->container->get('doctrine.orm.entity_manager');
        $allTables = $manager->getRepository(BundesligaTable::class)->findBy(['bundesliga_season' => null]);
        $season = $manager->getRepository(BundesligaSeason::class)->findOneBy(['actual_season' => 1]);

        foreach ($allTables as $table) {
            //season
            $table->setBundesligaSeason($season);

            //team
            $teamName = $table->getTeam();
            if (false !== strpos($teamName, 'Leipzig Glueck Auf!')) {
                $teamName = 'Leipzig Glück Auf!';
            }
            $team = $manager->getRepository(BundesligaTeam::class)->findOneBy(['name' => $teamName]);
            $table->setBundesligaTeam($team);
        }
        $manager->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
