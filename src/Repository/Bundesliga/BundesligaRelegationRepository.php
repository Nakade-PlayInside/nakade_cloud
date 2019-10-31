<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaRelegation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaRelegation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaRelegation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaRelegation[]    findAll()
 * @method BundesligaRelegation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaRelegationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaRelegation::class);
    }

    // /**
    //  * @return BundesligaRelegation[] Returns an array of BundesligaRelegation objects
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
    public function findOneBySomeField($value): ?BundesligaRelegation
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
