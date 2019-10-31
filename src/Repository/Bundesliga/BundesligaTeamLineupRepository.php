<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaTeamLineup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaTeamLineup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaTeamLineup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaTeamLineup[]    findAll()
 * @method BundesligaTeamLineup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaTeamLineupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaTeamLineup::class);
    }

    // /**
    //  * @return BundesligaTeamLineup[] Returns an array of BundesligaTeamLineup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BundesligaTeamLineup
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
