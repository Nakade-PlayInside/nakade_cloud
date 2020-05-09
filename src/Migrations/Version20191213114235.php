<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Bundesliga\BundesligaTable;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191213114235 extends AbstractMigration implements ContainerAwareInterface
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
//        $manager = $this->container->get('doctrine.orm.entity_manager');
//        $allTables = $manager->getRepository(BundesligaTable::class)->findAll();
//        foreach ($allTables as $table) {
//            if (empty($table->getImgSrc())) {
//                continue;
//            }
//            $parts = pathinfo($table->getImgSrc());
//            $basename = $parts['basename'];
//            $table->setImgSrc($basename);
//        }
//        $manager->flush();
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
