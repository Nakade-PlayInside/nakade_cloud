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

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Services\Model\TableModel;

/**
 * Find table by season.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaTableService extends AbstractTableService
{
    public function retrieveTable(string $seasonId = null, int $matchDay = null): ?TableModel
    {
        $season = $this->findSeason($seasonId);
        if (!$season) {
            return null;
        }

        $lastMatchDay = (int) $this->findLastMatchDay($season);
        if (!$lastMatchDay) {
            return null;
        }

        //no provided matchDay, find last matchDay
        if (!$matchDay) {
            $matchDay = $lastMatchDay;
        }

        //offset for last match played
        if ($matchDay > $lastMatchDay) {
            $matchDay = $lastMatchDay;
        }

        //problem league 5! more than 10 teams
        if ($matchDay > $season->getNoMatchDays()) {
            $matchDay = $season->getNoMatchDays();
        }

        $model = new TableModel($season, $matchDay, (string) $lastMatchDay);
        $matchDayRange = $this->calcMatchDayRange($season);
        $model->setMatchDayRange($matchDayRange);

        //find table
        $table = $this->findActualTable($season, $matchDay);
        if (!$table) {
            return null;
        }

        //get result if set
        $result = $this->manager->getRepository(BundesligaResults::class)->findNakadeResult($season->getId(), $matchDay);
        if ($result) {
            $model->setResult($result);
        }
        //next match pairing
        $nextResult = $this->manager->getRepository(BundesligaResults::class)->findNakadeResult($season->getId(), $matchDay + 1);
        if ($nextResult) {
            $model->setNextResult($nextResult);
        }

        return $model->setActualTable($table);
    }

    private function findSeason(string $seasonId = null): ?BundesligaSeason
    {
        //if no season provided, find actual season
        if (!$seasonId) {
            return $this->findActualSeason();
        }

        return $this->manager->getRepository(BundesligaSeason::class)->find($seasonId);
    }

    private function calcMatchDayRange(BundesligaSeason $actualSeason): array
    {
        $maxMatchDays = $actualSeason->getTeams()->count() - 1;

        return range(1, $maxMatchDays);
    }
}
