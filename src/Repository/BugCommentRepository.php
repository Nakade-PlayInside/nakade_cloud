<?php

namespace App\Repository;

use App\Entity\BugComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BugComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method BugComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method BugComment[]    findAll()
 * @method BugComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BugCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BugComment::class);
    }

    // /**
    //  * @return BugComment[] Returns an array of BugComment objects
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
    public function findOneBySomeField($value): ?BugComment
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
