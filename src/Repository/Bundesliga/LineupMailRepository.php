<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\LineupMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LineupMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method LineupMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method LineupMail[]    findAll()
 * @method LineupMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineupMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineupMail::class);
    }

    // /**
    //  * @return LineupMail[] Returns an array of LineupMail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LineupMail
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
