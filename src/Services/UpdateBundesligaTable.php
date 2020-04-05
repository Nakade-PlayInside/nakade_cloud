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
use App\Services\UpdateTableLogic\TableGenerator;
use App\Services\UpdateTableLogic\TablePositioner;
use App\Services\UpdateTableLogic\TableSorter;
use App\Services\UpdateTableLogic\TableStatsGenerator;
use App\Services\UpdateTableLogic\TableTendency;
use Doctrine\ORM\EntityManagerInterface;

class UpdateBundesligaTable
{
    private $manager;
    private $tableGenerator;
    private $statsGenerator;
    private $sorter;
    private $positioner;
    private $tendency;

    public function __construct(EntityManagerInterface $manager, TableGenerator $tableGenerator)
    {
        $this->manager = $manager;
        $this->tableGenerator = $tableGenerator;

        $this->statsGenerator = new TableStatsGenerator();
        $this->sorter = new TableSorter();
        $this->positioner = new TablePositioner();
        $this->tendency = new TableTendency();
    }

    public function handle(BundesligaSeason $season, int $matchDay): void
    {
        $results = $this->manager->getRepository(BundesligaResults::class)->findResultsByMatchDay($season, $matchDay);
        if (empty($results)) {
            $msg = sprintf('No results in season <%s> for match day <%s> in league <%s> found!', $season->getTitle(), $season->getLeague(), $matchDay);
            throw new \LogicException($msg);
        }

        $tables = [];
        foreach ($results as $result) {
            $pairing = $this->processResult($result);
            $tables = array_merge($tables, $pairing);
        }

        $this->sorter->sortTable($tables);
        $this->positioner->create($tables);
        $this->tendency->create($season, $tables);

        $this->manager->flush();
    }

    //update
    //calculate position by points, board points and previous position
    //compare to previous position for background and tendency

    private function processResult(BundesligaResults $result): array
    {
        //generate new table
        $home = $this->tableGenerator->createTable($result, $result->getHome());
        $away = $this->tableGenerator->createTable($result, $result->getAway());

        //create stats
        $this->statsGenerator->create($home, $away, $result);

        $this->manager->persist($home);
        $this->manager->persist($away);

        return [$home, $away];

    }
}
