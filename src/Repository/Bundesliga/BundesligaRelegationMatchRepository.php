<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaRelegationMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaRelegationMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaRelegationMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaRelegationMatch[]    findAll()
 * @method BundesligaRelegationMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaRelegationMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaRelegationMatch::class);
    }

    // /**
    //  * @return BundesligaRelegationMatch[] Returns an array of BundesligaRelegationMatch objects
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
    public function findOneBySomeField($value): ?BundesligaRelegationMatch
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
