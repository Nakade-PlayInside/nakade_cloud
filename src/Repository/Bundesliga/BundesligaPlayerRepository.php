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

use App\Entity\Bundesliga\BundesligaPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BundesligaPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method BundesligaPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method BundesligaPlayer[]    findAll()
 * @method BundesligaPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BundesligaPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BundesligaPlayer::class);
    }

    public function findPlayerByIdWithSeasons($id)
    {
        $queryBuilder = $this->createQueryBuilder('p')
               ->where('p.id = :player_id')
               ->addSelect('seasons')
               ->leftJoin('p.seasons', 'seasons')
               ->setParameter('player_id', $id);

        $query = $queryBuilder->getQuery();

        return $query->getOneOrNullResult();
    }

    /*
    public function findOneBySomeField($value): ?Player
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
