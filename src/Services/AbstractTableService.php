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

namespace App\Services;

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractTableService
{
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    protected function findActualSeason(): ?BundesligaSeason
    {
        $actualSeason = $this->manager->getRepository(BundesligaSeason::class)->findOneBy(['actualSeason' => true]);
        if ($actualSeason) {
            return $actualSeason;
        }

        return $this->manager->getRepository(BundesligaSeason::class)->findLastSeason();
    }

    protected function findLastMatchDay(BundesligaSeason $actualSeason): ?string
    {
        return $this->manager->getRepository(BundesligaTable::class)
                ->findLastMatchDay($actualSeason->getDGoBIndex(), $actualSeason->getLeague());
    }


    protected function findActualTable(BundesligaSeason $actualSeason, $matchDay = '1'): array
    {
        return $this->manager->getRepository(BundesligaTable::class)->findBy(
            [
                'season' => $actualSeason->getDGoBIndex(),
                'league' => $actualSeason->getLeague(),
                'matchDay' => $matchDay,
            ],
            ['position' => 'ASC']
        );
    }
}
