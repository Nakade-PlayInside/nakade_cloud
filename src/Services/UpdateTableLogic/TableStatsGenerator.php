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

namespace App\Services\UpdateTableLogic;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaTable;

class TableStatsGenerator
{
    /**
     * Creates table stats from result for home and away team.
     */
    public function create(BundesligaTable $home, BundesligaTable $away, BundesligaResults $result): void
    {
        if ($home->getBundesligaTeam() !== $result->getHome() || $away->getBundesligaTeam() !== $result->getAway()) {
            throw new \LogicException('Provided table does not match!');
        }

        //if not played no update needed
        if (!$this->isPlayed($result)) {
            return;
        }

        //add Games
        $home->addGame();
        $away->addGame();

        //board points
        $home->addBoardPoints($result->getBoardPointsHome());
        $away->addBoardPoints($result->getBoardPointsAway());

        //wins
        if ($result->getBoardPointsHome() > $result->getBoardPointsAway()) {
            $home->addWin();
            $away->addLoss();
            $home->addPoints(2);
        }
        //draws
        if ($result->getBoardPointsHome() === $result->getBoardPointsAway()) {
            $home->addDraw();
            $away->addDraw();
            $home->addPoints(1);
            $away->addPoints(1);
        }
        //loss
        if ($result->getBoardPointsHome() < $result->getBoardPointsAway()) {
            $home->addLoss();
            $away->addWin();
            $away->addPoints(2);
        }
    }

    private function isPlayed(BundesligaResults $result): bool
    {
        if (!$result->getBoardPointsHome() || !$result->getBoardPointsAway()) {
            return false;
        }

        if (0 === $result->getBoardPointsHome() && 0 === $result->getBoardPointsAway()) {
            return false;
        }

        return true;
    }
}
