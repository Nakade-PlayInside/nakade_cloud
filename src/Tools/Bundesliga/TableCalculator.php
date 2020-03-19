<?php

declare(strict_types=1);
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2020 Dr. Holger Maerz
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

namespace App\Tools\Bundesliga;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Tools\Bundesliga\Model\TableModel;
use App\Tools\Bundesliga\Model\TeamModel;
use Doctrine\ORM\EntityManagerInterface;

class TableCalculator
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function find(BundesligaSeason $season, int $matchDay)
    {
        $allResults = $this->manager
                ->getRepository(BundesligaResults::class)
                ->findResultsByMatchDay($season, $matchDay);

        $tableModel = new TableModel();
        foreach ($allResults as $result) {
            $team = $tableModel->addTeam(new TeamModel($result->getHome()));
            $this->addPoints($team, $result->getBoardPointsHome());

            $team = $tableModel->addTeam(new TeamModel($result->getAway()));
            $this->addPoints($team, $result->getBoardPointsAway());
        }

        if (1 === $matchDay) {
            //todo
        }

        $prevMatchDay = $matchDay - 1;
        $prevTable = $this->getTable($season, $prevMatchDay);
        $actualTable = $this->getTable($season, $matchDay);

        foreach ($prevTable as $table) {
            $teamName = $table->getBundesligaTeam()->getName();

            /** @var TeamModel $teamModel */
            $teamModel = $tableModel->teams[$teamName];
            $teamModel->boardPoints += $table->getBoardPoints();
            $teamModel->points += (int) $table->getPoints();
            $teamModel->wins += (int) $table->getWins();
            $teamModel->draws += (int) $table->getDraws();
            $teamModel->losses += (int) $table->getLosses();
            $teamModel->games = (int) $table->getGames() + 1;

            if (array_key_exists($table->getBundesligaTeam()->getName(), $actualTable)) {
                $actualTeamTable = $actualTable[$table->getBundesligaTeam()->getName()];
                $teamModel->firstBoardPoints = $actualTeamTable->getFirstBoardPoints();
                $teamModel->boardPoints -= $actualTeamTable->getPenalty();
            }
            $tableModel->teams[$teamName] = $teamModel;
        }

        $tableModel->sortTeams();
        $teamTable = $tableModel->teams;
        $position = 0;
        $league = (int) $season->getLeague();

        foreach ($teamTable as $teamModel) {
            $teamModel->position = ++$position;
            $prev = $prevTable[$teamModel->team->getName()];

            if ($prev->getPosition() > $position) {
                $teamModel->imgSrc = 'lmo-tab1.gif';
            }
            if ($prev->getPosition() < $position) {
                $teamModel->imgSrc = 'lmo-tab2.gif';
            }
            $teamModel->tendency = $this->getTendency($position, $league);
        }

        dd($teamTable);

        //tendency by league
        //lookup for
    }

    private function getTendency(int $position, int $league): ?int
    {
        if (1 === $position) {
            return 10;
        }
        if (2 === $position && $league > 1) {
            return 20;
        }
        if ($position > 8) {
            return 40;
        }
        if (8 === $position && 2 === $league) {
            return 30;
        }

        return null;
    }

    /**
     * @return BundesligaTable[] array
     */
    private function getTable(BundesligaSeason $season, int $matchDay): array
    {
        $tables = $this->manager
                ->getRepository(BundesligaTable::class)
                ->findTableByMatchDay($season, $matchDay);

        $actualTable = [];
        foreach ($tables as $table) {
            $actualTable[$table->getBundesligaTeam()->getName()] = $table;
        }

        return $actualTable;
    }

    private function addPoints(TeamModel $team, int $boardPoints)
    {
        $team->boardPoints += $boardPoints;
        if (4 === $boardPoints) {
            ++$team->points;
            ++$team->draws;
        }
        if ($boardPoints > 4) {
            $team->points += 2;
            ++$team->wins;
        }
        if ($boardPoints < 4) {
            ++$team->losses;
        }
    }
}
