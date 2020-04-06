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
use App\Entity\Bundesliga\BundesligaTeam;
use App\Repository\Bundesliga\BundesligaTableRepository;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TableGenerator
{
    private $repository;

    public function __construct(BundesligaTableRepository $repository) {

        $this->repository = $repository;
    }

    /**
     * Generates a new table from previous table data. If no previous data exist, default data are set.
     */
    public function createTable(BundesligaResults $result, BundesligaTeam $team): BundesligaTable
    {
        $table = new BundesligaTable();
        $table->setBundesligaSeason($result->getSeason())
                ->setBundesligaTeam($team)
                ->setMatchDay($result->getMatchDay())
        ;

        $previousTable = null;
        if ($result->getMatchDay() > 1) {
            $prevMatchDay = $result->getMatchDay() - 1;
            $previousTable = $this->repository->findTableByTeamAndMatchDay($result->getSeason(), $team, $prevMatchDay);
        }

        if ($previousTable) {
            $table->setBoardPoints($previousTable->getBoardPoints())
                ->setPoints($previousTable->getPoints())
                ->setGames($previousTable->getGames())
                ->setWins($previousTable->getWins())
                ->setDraws($previousTable->getDraws())
                ->setLosses($previousTable->getLosses())
                ->setPosition($previousTable->getPosition())
                ->setFirstBoardPoints($previousTable->getFirstBoardPoints()) //important for sorting
                ;
        }

        return $table;
    }
}
