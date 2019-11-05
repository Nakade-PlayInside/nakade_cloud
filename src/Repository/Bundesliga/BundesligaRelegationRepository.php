<?php

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaRelegation;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method BundesligaRelegation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaRelegation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaRelegation[]    findAll()
 * @method BundesligaRelegation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaRelegationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaRelegation::class);
    }


    public function findBySeason($seasonId)
    {
        return $this->createQueryBuilder('r')
                ->innerJoin('r.season', 's')
                ->innerJoin(BundesligaTeam::class, 'h', expr\Join::WITH, 'h.id=r.home')
                ->innerJoin(BundesligaTeam::class, 'a', expr\Join::WITH, 'a.id=r.away')
                ->where('s.id=:id')
                ->andWhere('h.name LIKE :team OR a.name LIKE :team')
                ->setParameter('id', $seasonId)
                ->setParameter('team', '%Nakade%')
                ->orderBy('r.round', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }
}
