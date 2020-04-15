<?php
declare(strict_types=1);
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

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BundesligaTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaTable[]    findAll()
 * @method BundesligaTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaTable::class);
    }

    /**
     * used for actual results.
     * here you must use max
     */
    public function findLastMatchDay(BundesligaSeason $season): ?string
    {
        try {
            return $this->createQueryBuilder('t')
                    ->select('MAX(t.matchDay) as lastMatchDay')
                    ->andWhere('t.bundesligaSeason=:season')
                    ->setParameter('season', $season)
                    ->getQuery()
                    ->getSingleScalarResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * used for updating
     */
    public function findTableByTeamAndMatchDay(BundesligaSeason $season, BundesligaTeam $team, int $matchDay): ?BundesligaTable
    {
        try {
            return $this->createQueryBuilder('t')
                    ->andWhere('t.bundesligaSeason=:season')
                    ->andWhere('t.bundesligaTeam=:team')
                    ->andWhere('t.matchDay=:matchDay')
                    ->setParameter('season', $season)
                    ->setParameter('team', $team)
                    ->setParameter('matchDay', $matchDay)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return BundesligaTable[] array
     *
     * @deprecated
     */
    public function findTableByMatchDay(BundesligaSeason $season, int $matchDay): array
    {
        return $this->createQueryBuilder('t')
                ->andWhere('t.season LIKE :season')
                ->andWhere('t.matchDay=:matchDay')
                ->setParameter('season', '%'.$season->getDGoBIndex().'%')
                ->setParameter('matchDay', $matchDay)
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * @return BundesligaTable[] array
     */
    public function findTablesBySeasonAndMatchDay(BundesligaSeason $season, int $matchDay): array
    {
        return $this->createQueryBuilder('t')
                ->andWhere('t.bundesligaSeason=:season')
                ->andWhere('t.matchDay=:matchDay')
                ->setParameter('season', $season)
                ->setParameter('matchDay', $matchDay)
                ->getQuery()
                ->getResult()
                ;
    }
}
