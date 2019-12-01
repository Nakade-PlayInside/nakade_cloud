<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\ResultMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResultMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultMail[]    findAll()
 * @method ResultMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultMail::class);
    }

    // /**
    //  * @return ResultMail[] Returns an array of ResultMail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResultMail
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
