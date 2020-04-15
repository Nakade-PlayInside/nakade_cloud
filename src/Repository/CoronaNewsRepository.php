<?php

namespace App\Repository;

use App\Entity\CoronaNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoronaNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoronaNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoronaNews[]    findAll()
 * @method CoronaNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoronaNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoronaNews::class);
    }

    // /**
    //  * @return CoronaNews[] Returns an array of CoronaNews objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CoronaNews
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
