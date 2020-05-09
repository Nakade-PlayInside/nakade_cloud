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
final class Version20200120105555 extends AbstractMigration implements ContainerAwareInterface
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
//        $manager = $this->container->get('doctrine.orm.entity_manager');
//        $allTables = $manager->getRepository(BundesligaTable::class)->findAll();
//        foreach ($allTables as $table) {
//            //season
//            $season = $table->getSeason();
//            $matches = explode('_', $season);
//            $title = 'Saison '.array_shift($matches).'/'.substr(array_pop($matches), 2);
//            $blSeason = $manager->getRepository(BundesligaSeason::class)->findOneBy(['title' => $title]);
//            if ($blSeason) {
//                $table->setBundesligaSeason($blSeason);
//            }
//
//            //team
//            $teamName = $table->getTeam();
//            $blTeam = $manager->getRepository(BundesligaTeam::class)->findOneBy(['name' => $teamName]);
//            if (!$blTeam) {
//                $modified = str_replace('ue', 'ü', $teamName);
//                $modified = str_replace('ae', 'ä', $modified);
//                if (false !== strpos($modified, 'MoinMoin Hamburg')) {
//                    $modified = 'MoinMoin HH';
//                }
//                if (false !== strpos($modified, 'HanseGO Bremen')) {
//                    $modified = 'HanseGo Bremen 1';
//                }
//                if (false !== strpos($modified, 'Leipzig Glück Auf')) {
//                    $modified = 'Leipzig Glück Auf!';
//                }
//                $blTeam = $manager->getRepository(BundesligaTeam::class)->findOneBy(['name' => $modified]);
//            }
//            if (!$blTeam) {
//                $blTeam = $manager->getRepository(BundesligaTeam::class)->findSimilarTeam($teamName);
//            }
//            $table->setBundesligaTeam($blTeam);
//        }
//        $manager->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
