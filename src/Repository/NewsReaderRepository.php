<?php

namespace App\Repository;

use App\Entity\NewsReader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NewsReader|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsReader|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsReader[]    findAll()
 * @method NewsReader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsReaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsReader::class);
    }

    // /**
    //  * @return NewsReader[] Returns an array of NewsReader objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewsReader
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
