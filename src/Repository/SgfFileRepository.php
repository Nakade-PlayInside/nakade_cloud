<?php

namespace App\Repository;

use App\Entity\SgfFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SgfFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method SgfFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method SgfFile[]    findAll()
 * @method SgfFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SgfFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SgfFile::class);
    }

    // /**
    //  * @return SgfFile[] Returns an array of SgfFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SgfFile
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
