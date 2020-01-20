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
final class Version20200120175555 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }

    public function postUp(Schema $schema): void
    {
        /** @var EntityManager $manager */
        $manager = $this->container->get('doctrine.orm.entity_manager');
        $actualSeason = $manager->getRepository(BundesligaSeason::class)->findOneBy(['actualSeason' => true]);

        $frankfurt = $manager->getRepository(BundesligaTeam::class)->findOneBy(['name' => 'Frankfurter Plateauniker']);
        $tblFfm = $this->findTable($manager, $actualSeason, $frankfurt);
        $tblFfm->setFirstBoardPoints(1);

        $bonn = $manager->getRepository(BundesligaTeam::class)->findOneBy(['name' => 'Uni Bonn']);
        $tblBonn = $this->findTable($manager, $actualSeason, $bonn);
        $tblBonn->setFirstBoardPoints(3)
                ->setPenalty(1);

        $bremen = $manager->getRepository(BundesligaTeam::class)->findOneBy(['name' => 'HanseGo Bremen 1']);
        $tblBremen = $this->findTable($manager, $actualSeason, $bremen);
        $tblBremen->setFirstBoardPoints(2);

        $manager->flush();
    }

    private function findTable(EntityManager $manager, BundesligaSeason $season, BundesligaTeam $team): BundesligaTable
    {
        /** @var BundesligaTable $teamTable */
        $teamTable = $manager
                ->getRepository(BundesligaTable::class)
                ->findOneBy(
                        [
                                'bundesligaSeason' => $season,
                                'matchDay' => 5,
                                'bundesligaTeam' => $team,
                        ]
                );

        return $teamTable;
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
