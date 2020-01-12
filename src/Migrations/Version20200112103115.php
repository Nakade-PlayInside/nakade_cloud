<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Bundesliga\BundesligaSgf;
use App\Entity\Bundesliga\BundesligaTable;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200112103115 extends AbstractMigration  implements ContainerAwareInterface
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
        $files = $manager->getRepository(BundesligaSgf::class)->findBy(['hash' => null]);
        foreach ($files as $sgf) {
            $path = realpath('public/'.$sgf->getPath());
            if (!is_file($path)) {
                continue;
            }
            $hash = md5_file($path);
            $sgf->setHash($hash);
        }
        $manager->flush();
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
