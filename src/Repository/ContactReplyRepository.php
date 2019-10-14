<?php

namespace App\Repository;

use App\Entity\ContactReply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContactReply|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactReply|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactReply[]    findAll()
 * @method ContactReply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactReply::class);
    }

    // /**
    //  * @return ContactResponse[] Returns an array of ContactResponse objects
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
    public function findOneBySomeField($value): ?ContactResponse
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
