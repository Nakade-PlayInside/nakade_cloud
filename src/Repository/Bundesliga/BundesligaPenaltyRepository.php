<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaPenalty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaPenalty|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaPenalty|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaPenalty[]    findAll()
 * @method BundesligaPenalty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaPenaltyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaPenalty::class);
    }

    // /**
    //  * @return BundesligaPenalty[] Returns an array of BundesligaPenalty objects
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
    public function findOneBySomeField($value): ?BundesligaPenalty
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
