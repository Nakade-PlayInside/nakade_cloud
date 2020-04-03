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

namespace App\Services;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\ORM\EntityManagerInterface;

class UpdateBundesligaTable
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    //update
    //calculate position by points, board points and previous position
    //compare to previous position for background and tendency

    private function processResult(BundesligaResults $result)
    {

        //gibt es previous => Saisonbeginn
        //gibt es schon actuelles ergebnis? dann ist es ein update
        $homeTeamTable = $this->manager
                ->getRepository(BundesligaTable::class)
                ->findTableByTeamAndMatchDay($result->getSeason(), $result->getHome(), $result->getMatchDay()-1);

        $awayTeamTable = $this->manager
                ->getRepository(BundesligaTable::class)
                ->findTableByTeamAndMatchDay($result->getSeason(), $result->getAway(), $result->getMatchDay()-1);

        $home = new BundesligaTable();
        $home->

        //add Games
        $homeTeamTable->addGame();
        $awayTeamTable->addGame();

        //board points
        $homeTeamTable->addBoardPoints($result->getBoardPointsHome());
        $awayTeamTable->addBoardPoints($result->getBoardPointsAway());

        //wins
        if ($result->getBoardPointsHome() > $result->getBoardPointsAway()) {
            $homeTeamTable->addWin();
            $awayTeamTable->addLoss();
            $homeTeamTable->addPoints(2);
        }
        //draws
        if ($result->getBoardPointsHome() === $result->getBoardPointsAway()) {
            $homeTeamTable->addDraw();
            $awayTeamTable->addDraw();;
            $homeTeamTable->addPoints(1);
            $awayTeamTable->addPoints(1);
        }
        //loss
        if ($result->getBoardPointsHome() < $result->getBoardPointsAway()) {
            $homeTeamTable->addLoss();
            $awayTeamTable->addWin();
            $awayTeamTable->addPoints(2);
        }

        $teamTable = $this->manager
                ->getRepository(BundesligaTable::class)
                ->findTableByTeamAndMatchDay($result->getSeason(), $result->getHome(), $result->getMatchDay()-1);




        //muss die teams einzeln updaten -> ermöglicht temporäre tabelle
    }



    private function handleResult(BundesligaSeason $season, BundesligaTeam $team, int $matchDay)
    {
        $previousMatchDay = $matchDay-1;
        $teamTable = $this->manager->getRepository(BundesligaTable::class)->findTableByTeamAndMatchDay($season, $team, $matchDay-1);

    }

    private function getPreviousTeamTable(BundesligaSeason $season, BundesligaTeam $team, int $matchDay): array
    {
        $previousMatchDay = $matchDay - 1;

        return $this->manager->getRepository(BundesligaTable::class)->findTableByTeamAndMatchDay($season, $team, $matchDay);
    }

    private function hasPreviousTable(BundesligaSeason $season, int $matchDay): bool
    {
        $previousMatchDay = $matchDay - 1;

        return !empty($this->manager->getRepository(BundesligaTable::class)->findTableByMatchDay($season, $previousMatchDay));
    }

    private function hasUpdatedTable(BundesligaSeason $season, int $matchDay): bool
    {
        return !empty($this->manager->getRepository(BundesligaTable::class)->findTableByMatchDay($season, $matchDay));
    }

    private function getResults(BundesligaSeason $season, int $matchDay): array
    {
        $results = $this->manager->getRepository(BundesligaResults::class)->findResultsByMatchDay($season, $matchDay);
        if (empty($results)) {
            $msg = sprintf('No results in season <%s> for match day <%s> in league <%s> found!', $season->getTitle(), $season->getLeague(), $matchDay);
            throw new \LogicException($msg);
        }

        return $results;
    }
}
