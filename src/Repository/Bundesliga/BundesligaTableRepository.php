<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaTable[]    findAll()
 * @method BundesligaTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaTable::class);
    }

    /**
     * used for actual results grabber.
     */
    public function findLastMatchDay(string $season, string $league): ?string
    {
        try {
            return $this->createQueryBuilder('t')
                    ->select('MAX(t.matchDay) as lastMatchDay')
                    ->where('t.season LIKE :season')
                    ->andWhere('t.league LIKE :league')
                    ->setParameter('season', '%'.$season.'%')
                    ->setParameter('league', '%'.$league.'%')
                    ->getQuery()
                    ->getSingleScalarResult();
        } catch (\Exception $e) {
            return null;
        }
    }
}
