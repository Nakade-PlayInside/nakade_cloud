<?php
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2019 Dr. Holger Maerz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Repository\Bundesliga;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use App\Services\Model\ResultsModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method BundesligaResults|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaResults|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaResults[]    findAll()
 * @method BundesligaResults[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaResults::class);
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
                ->orderBy('r.matchDay', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }

    public function findNakadeResult(int $seasonId, int $matchDay): ?BundesligaResults
    {
        try {
            return $this->createQueryBuilder('r')
                    ->innerJoin('r.season', 's')
                    ->innerJoin(BundesligaTeam::class, 'h', expr\Join::WITH, 'h.id=r.home')
                    ->innerJoin(BundesligaTeam::class, 'a', expr\Join::WITH, 'a.id=r.away')
                    ->where('s.id=:id')
                    ->andWhere('h.name LIKE :team OR a.name LIKE :team')
                    ->andWhere('r.matchDay= :matchDay')
                    ->setParameter('id', $seasonId)
                    ->setParameter('team', '%Nakade%')
                    ->setParameter('matchDay', $matchDay)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
        } catch (\Exception $e) {
            return null;
        }
    }

    //used in BUndesligaController
    public function findActualMatchDay(BundesligaSeason $season): ?string
    {
        try {
            return $this->createQueryBuilder('r')
                    ->innerJoin('r.season', 's')
                    ->innerJoin(BundesligaTeam::class, 'h', expr\Join::WITH, 'h.id=r.home')
                    ->innerJoin(BundesligaTeam::class, 'a', expr\Join::WITH, 'a.id=r.away')
                    ->andWhere('s.id=:id')
                    ->andWhere('h.name LIKE :team OR a.name LIKE :team')
                    ->andWhere('r.boardPointsHome=0 AND r.boardPointsAway=0')
                    ->setParameter('id', $season)
                    ->setParameter('team', '%Nakade%')
                    ->select('MIN(r.matchDay) as actualMatchDay')
                    ->getQuery()
                    ->getSingleScalarResult()
                    ;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * used for table fixtures.
     *
     * @return BundesligaResults[]
     */
    public function findPositionTable(BundesligaSeason $season, int $matchDay): array
    {
        return $this->createQueryBuilder('r')
                    ->leftJoin('r.season', 's')
                    ->andWhere('s.id=:id')
                    ->andWhere('r.matchDay <= :matchDay')
                    ->setParameter('id', $season)
                    ->setParameter('matchDay', $matchDay)
                    ->getQuery()
                    ->getResult()
                    ;
    }

    /**
     * used for match fixtures.
     *
     * @return BundesligaResults[]|null
     */
    public function findNakadeResultsBySeason(BundesligaSeason $season): array
    {
        return $this->createQueryBuilder('r')
                ->innerJoin('r.season', 's')
                ->innerJoin(BundesligaTeam::class, 'h', expr\Join::WITH, 'h.id=r.home')
                ->innerJoin(BundesligaTeam::class, 'a', expr\Join::WITH, 'a.id=r.away')
                ->where('s.id=:season')
                ->andWhere('h.name LIKE :team OR a.name LIKE :team')
                ->setParameter('season', $season)
                ->setParameter('team', '%Nakade%')
                ->getQuery()
                ->getResult()
                ;
    }

    //used in ResultsCatcher
    public function findPairingUnplayed(BundesligaSeason $season, ResultsModel $model): ?BundesligaResults
    {
        try {
            return $this->createQueryBuilder('r')
                    ->innerJoin('r.season', 's')
                    ->innerJoin(BundesligaTeam::class, 'h', expr\Join::WITH, 'h.id=r.home')
                    ->innerJoin(BundesligaTeam::class, 'a', expr\Join::WITH, 'a.id=r.away')
                    ->andWhere('s.id=:id')
                    ->andWhere('h.name LIKE :homeTeam OR a.name LIKE :awayTeam')
                    ->andWhere('r.matchDay=:matchDay')
                    ->andWhere('r.boardPointsHome=0 AND r.boardPointsAway=0')
                    ->setParameter('id', $season)
                    ->setParameter('homeTeam', '%'.$model->homeTeam.'%')
                    ->setParameter('awayTeam', '%'.$model->awayTeam.'%')
                    ->setParameter('matchDay', $model->getMatchDay())
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
        } catch (\Exception $e) {
            return null;
        }
    }

    //used in ResultsCatcher
    public function findMatchDayUnplayed(BundesligaSeason $season): ?string
    {
        try {
            return $this->createQueryBuilder('r')
                    ->select('MIN(r.matchDay) as lastMatchDay')
                    ->innerJoin('r.season', 's')
                    ->andWhere('s.id=:id')
                    ->andWhere('r.boardPointsHome=0 AND r.boardPointsAway=0')
                    ->setParameter('id', $season)
                    ->getQuery()
                    ->getSingleScalarResult()
                    ;
        } catch (\Exception $e) {
            return null;
        }
    }
}
