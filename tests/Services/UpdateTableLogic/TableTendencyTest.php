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
    private $tables = null;

    public function setUp(): void
    {
        $this->tables = $this->createTables();
        $this->tendency = new TableTendency();
    }

    /**
     * @dataProvider tendencyProvider
     */
    public function testTendencySecondLeague()
    {
        $season = $this->createSeason('2');
        $this->tendency->create($season, $this->tables);

        $this->assertEquals(BundesligaTable::TENDENCY_CHAMPION, $this->tables[1]->getTendency());
        $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $this->tables[2]->getTendency());

        if ('5' === $season->getLeague()) {
            $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $this->tables[3]->getTendency());
            $this->assertEquals(BundesligaTable::TENDENCY_AUFSTEIGER, $this->tables[4]->getTendency());
        } else {
            $this->assertNull($this->tables[3]->getTendency());
            $this->assertNull($this->tables[4]->getTendency());
        }
        $this->assertNull($this->tables[5]->getTendency());
        $this->assertNull($this->tables[6]->getTendency());
        $this->assertNull($this->tables[7]->getTendency());

        if ('2' === $season->getLeague()) {
            $this->assertEquals(BundesligaTable::TENDENCY_RELEGATION, $this->tables[8]->getTendency());
        } else {
            $this->assertNull($this->tables[8]->getTendency());
        }

        if ('5' !== $season->getLeague()) {
            $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $this->tables[9]->getTendency());
            $this->assertEquals(BundesligaTable::TENDENCY_ABSTEIGER, $this->tables[10]->getTendency());
        } else {
            $this->assertNull($this->tables[9]->getTendency());
            $this->assertNull($this->tables[10]->getTendency());
        }
    }

    public function tendencyProvider(): array
    {
        return [
                ['1'], ['2'], ['3a'], ['3b'], ['4a'], ['4b'], ['5'],
        ];
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
    private function createTables(): array
    {
        $tables = [];
        for ($i = 1; $i <= 10; ++$i) {
            $tables[$i] = (new BundesligaTable())->setPosition($i);
        }

        return $tables;
    }
}
