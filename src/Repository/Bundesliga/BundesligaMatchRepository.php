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

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaSeason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaMatch[]    findAll()
 * @method BundesligaMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaMatch::class);
    }

    /**
     * @return BundesligaMatch[]
     */
    public function findPlayerMatches(BundesligaSeason $season, BundesligaPlayer $player): array
    {
        return $this->createQueryBuilder('m')
                ->leftJoin('m.results', 'r')
                ->leftJoin('r.season', 's')
                ->leftJoin('m.player', 'p')
                ->andWhere('s.id=:season')
                ->andWhere('p.id=:player')
                ->setParameter('season', $season)
                ->setParameter('player', $player)
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * @return BundesligaMatch[]
     */
    public function findAllMatches(): array
    {
        return $this->createQueryBuilder('m')
                ->leftJoin('m.results', 'r')
                ->leftJoin('r.season', 's')
                ->leftJoin('m.player', 'p')
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * @return BundesligaMatch[]
     */
    public function findMatchesWithoutSgf(): array
    {
        return $this->createQueryBuilder('m')
                ->leftJoin('m.results', 'r')
                ->leftJoin('r.season', 's')
                ->leftJoin('m.player', 'p')
                ->andWhere('m.sgf is NULL')
                ->getQuery()
                ->getResult()
                ;
    }
}
