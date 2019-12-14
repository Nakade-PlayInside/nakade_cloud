<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaSgf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaSgf|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaSgf|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaSgf[]    findAll()
 * @method BundesligaSgf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaSgfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaSgf::class);
    }

    // /**
    //  * @return BundesligaSgf[] Returns an array of BundesligaSgf objects
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
    public function findOneBySomeField($value): ?BundesligaSgf
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
