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

namespace App\Tools;

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use App\Repository\Bundesliga\BundesligaResultsRepository;
use App\Tools\Model\TableStatsModel;

class TableStats
{
    private $table = [];
    private $repository;

    public function __construct(BundesligaResultsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getStats(BundesligaSeason $season, int $matchDay): array
    {
        $this->table = [];
        $results = $this->repository->findPositionTable($season, $matchDay);
        foreach ($results as $matchResult) {
            if ('0 : 0' === $matchResult->getResult()) {
                return [];
            }

            $home = $this->getTeam($matchResult->getHome());
            $homePoints = $matchResult->getBoardPointsHome();
            $this->addStandings($home, $homePoints);

            $away = $this->getTeam($matchResult->getAway());
            $awayPoints = $matchResult->getBoardPointsAway();
            $this->addStandings($away, $awayPoints);
        }

        usort($this->table, [$this, 'sortByPoints']);

        return $this->table;
    }

    public function sortByPoints(TableStatsModel $alice, TableStatsModel $bob)
    {
        if ($alice->points === $bob->points) {
            return $this->sortByBoardPoints($alice, $bob);
        }

        return $alice->points < $bob->points;
    }

    public function sortByBoardPoints(TableStatsModel $alice, TableStatsModel $bob)
    {
        if ($alice->boardPoints === $bob->boardPoints) {
            return false;
        }

        return $alice->boardPoints < $bob->boardPoints;
    }

    private function addStandings(TableStatsModel $model, int $boardPoints)
    {
        ++$model->games;
        $model->boardPoints += $boardPoints;

        switch ($boardPoints) {
            case 0:
            case 1:
            case 2:
            case 3:
                ++$model->losses;
                break;
            case 4:
                ++$model->draws;
                ++$model->points;
                break;
            default:
                ++$model->wins;
                $model->points += 2;
        }
    }

    private function getTeam(BundesligaTeam $team): TableStatsModel
    {
        if (!array_key_exists($team->getName(), $this->table)) {
            $model = new TableStatsModel($team);
            $this->table[$team->getName()] = $model;
        }

        return $this->table[$team->getName()];
    }
}
