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

    // /**
    //  * @return BundesligaTable[] Returns an array of BundesligaTable objects
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
    public function findOneBySomeField($value): ?BundesligaTable
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
