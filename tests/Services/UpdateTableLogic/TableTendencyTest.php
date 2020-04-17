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

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Services\UpdateTableLogic\TableTendency;
use PHPUnit\Framework\TestCase;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TableTendencyTest extends TestCase
{
    private $tendency = null;

    public function setUp(): void
    {
        $this->tendency = new TableTendency();
    }

    public function testTendencyFirstLeague()
    {
        $tables = $this->createTables('1');
        $this->tendency->create($tables);

        $this->assertEquals(BundesligaTable::TENDENCY_CHAMPION, $tables[0]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $tables[1]->getTendency());
        $this->assertNull($tables[2]->getTendency());
        $this->assertNull($tables[3]->getTendency());
        $this->assertNull($tables[3]->getTendency());
        $this->assertNull($tables[4]->getTendency());
        $this->assertNull($tables[5]->getTendency());
        $this->assertNull($tables[6]->getTendency());
        $this->assertNull($tables[7]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $tables[8]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $tables[9]->getTendency());
    }

    public function testTendencySecondLeague()
    {
        $tables = $this->createTables('2');
        $this->tendency->create($tables);

        $this->assertEquals(BundesligaTable::TENDENCY_CHAMPION, $tables[0]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $tables[1]->getTendency());
        $this->assertNull($tables[2]->getTendency());
        $this->assertNull($tables[3]->getTendency());
        $this->assertNull($tables[4]->getTendency());
        $this->assertNull($tables[5]->getTendency());
        $this->assertNull($tables[6]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_RELEGATION, $tables[7]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $tables[8]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $tables[9]->getTendency());
    }

    public function testTendencyThirdLeague()
    {
        $tables = $this->createTables('3a');
        $this->tendency->create($tables);

        $this->assertEquals(BundesligaTable::TENDENCY_CHAMPION, $tables[0]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $tables[1]->getTendency());
        $this->assertNull($tables[2]->getTendency());
        $this->assertNull($tables[3]->getTendency());
        $this->assertNull($tables[4]->getTendency());
        $this->assertNull($tables[5]->getTendency());
        $this->assertNull($tables[6]->getTendency());
        $this->assertNull($tables[7]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $tables[8]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $tables[9]->getTendency());
    }

    public function testTendencyFithLeague()
    {
        $tables = $this->createTables('5');
        $this->tendency->create($tables);

        $this->assertEquals(BundesligaTable::TENDENCY_CHAMPION, $tables[0]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $tables[1]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $tables[2]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $tables[3]->getTendency());
        $this->assertNull($tables[4]->getTendency());
        $this->assertNull($tables[5]->getTendency());
        $this->assertNull($tables[6]->getTendency());
        $this->assertNull($tables[7]->getTendency());
        $this->assertNull($tables[8]->getTendency());
        $this->assertNull($tables[9]->getTendency());
    }

    private function createSeason(string $league = '1'): BundesligaSeason
    {
        $mock = $this->getMockBuilder(BundesligaSeason::class)->getMock();
        $mock->expects(static::any())->method('getLeague')->willReturn($league);

        /* @var BundesligaSeason $mock */
        return $mock;
    }

    /**
     * @return BundesligaTable[]|array
     */
    private function createTables(string $league): array
    {
        $season = $this->createSeason($league);
        $tables = [];
        for ($i = 0; $i <= 9; ++$i) {
            $tables[$i] = (new BundesligaTable())->setPosition($i+1)->setBundesligaSeason($season);
        }

        return $tables;
    }
}
