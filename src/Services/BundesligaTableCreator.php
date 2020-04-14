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
use App\Entity\Bundesliga\BundesligaTable;
use App\Repository\Bundesliga\BundesligaTableRepository;
use App\Services\UpdateTableLogic\TableGenerator;
use App\Services\UpdateTableLogic\TableStatsGenerator;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaTableCreator
{
    private $tableGenerator;
    private $statsGenerator;

    public function __construct(BundesligaTableRepository $repository)
    {
        $this->tableGenerator = new TableGenerator($repository);
        $this->statsGenerator = new TableStatsGenerator();
    }

    /**
     * @param array | BundesligaResults $results
     *
     * @return array | BundesligaTable[]
     */
    public function create(array $results): array
    {
        $tables = [];
        foreach ($results as $result) {
            $pairing = $this->processResult($result);
            $tables = array_merge($tables, $pairing);
        }

        return $tables;
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

        return [$home, $away];
    }
}
