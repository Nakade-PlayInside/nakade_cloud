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

namespace App\Tests\Services\UpdateTableLogic;

use App\Entity\Bundesliga\BundesligaTable;
use App\Services\UpdateTableLogic\TableSorter;
use PHPUnit\Framework\TestCase;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TableSorterTest extends TestCase
{
    private $sorter = null;

    public function setUp(): void
    {
        $this->sorter = new TableSorter();
    }

    public function testSortByPoints()
    {
        $tablesData = [
                [8, 0, 0, 1],
                [7, 0, 0, 2],
                [6, 0, 0, 3],
                [5, 0, 0, 4],
                [4, 0, 0, 5],
                [3, 0, 0, 6],
                        ];

        $tables = $this->createTables($tablesData);
        shuffle($tables);

        $this->sorter->sortTable($tables);

        $expectedPos = 1;
        foreach ($tables as $table) {
            $this->assertEquals($expectedPos++, $table->getPosition());
        }
    }

    public function testSortByPointsAndBoardPoints()
    {
        $tablesData = [
                [8, 16, 0, 1],
                [8, 14, 0, 2],
                [6, 8, 0, 3],
                [6, 7, 0, 4],
                [6, 5, 0, 5],
                [3, 2, 0, 6],
                [2, 4, 0, 7],
                [1, 5, 0, 8],
        ];

        $tables = $this->createTables($tablesData);
        shuffle($tables);

        $this->sorter->sortTable($tables);

        $expectedPos = 1;
        foreach ($tables as $table) {
            $this->assertEquals($expectedPos++, $table->getPosition());
        }
    }

    public function testSortByAll()
    {
        $tablesData = [
                [8, 16, 0, 1],
                [8, 14, 0, 2],
                [6, 7, 4, 3],
                [6, 7, 3, 4],
                [6, 5, 0, 5],
                [3, 2, 0, 6],
                [2, 4, 0, 7],
                [1, 5, 5, 8],
                [1, 5, 4, 9],
                [1, 5, 2, 10],
        ];

        $tables = $this->createTables($tablesData);
        shuffle($tables);

        $this->sorter->sortTable($tables);

        $expectedPos = 1;
        foreach ($tables as $table) {
            $this->assertEquals($expectedPos++, $table->getPosition());
        }
    }

    /**
     * @return BundesligaTable[]
     */
    private function createTables(array $tablesData): array
    {
        $tables = [];
        foreach ($tablesData as $data) {
            $tables[] = $this->generate($data);
        }

        return $tables;
    }

    /**
     * @return BundesligaTable[]
     */
    private function generate(array $data): BundesligaTable
    {
        $table = new BundesligaTable();
        $table->setPoints($data[0])
            ->setBoardPoints($data[1])
            ->setFirstBoardPoints($data[2])
            ->setPosition($data[3])//this is the expected position
        ;

        return $table;
    }
}
