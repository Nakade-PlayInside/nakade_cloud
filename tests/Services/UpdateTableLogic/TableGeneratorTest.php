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

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Entity\Bundesliga\BundesligaTeam;
use App\Repository\Bundesliga\BundesligaTableRepository;
use App\Services\UpdateTableLogic\TableGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TableGeneratorTest extends TestCase
{
    private $generator = null;

    /**
     * @var BundesligaTeam|null
     */
    private $team;

    /**
     * @var BundesligaSeason|null
     */
    private $season;

    /**
     * @var BundesligaTable|null
     */
    private $prevTable;

    public function setUp(): void
    {
        $this->team = $this->getMockBuilder(BundesligaTeam::class)->getMock();
        $this->season = $this->getMockBuilder(BundesligaSeason::class)->getMock();
        $this->prevTable = $this->createTable();

        $repo = $this->createRepo();
        $this->generator = new TableGenerator($repo);
    }

    public function testCreateOnFirstMatchDay()
    {
        $results = $this->createResults();
        $table = $this->generator->createTable($results, $this->team);

        $this->assertEquals(1, $table->getMatchDay());
        $this->assertSame($this->team, $table->getBundesligaTeam());
        $this->assertSame($this->season, $table->getBundesligaSeason());

        $this->assertEquals(0, $table->getGames());
        $this->assertEquals(0, $table->getPoints());
        $this->assertEquals(0, $table->getBoardPoints());
        $this->assertEquals(0, $table->getWins());
        $this->assertEquals(0, $table->getDraws());
        $this->assertEquals(0, $table->getLosses());
        $this->assertEquals(0, $table->getFirstBoardPoints());
        $this->assertNull($table->getPosition());
    }

    public function testMatchDay()
    {
        $results = $this->createResults(7);
        $table = $this->generator->createTable($results, $this->team);

        $this->assertEquals(7, $table->getMatchDay());
        $this->assertSame($this->team, $table->getBundesligaTeam());
        $this->assertSame($this->season, $table->getBundesligaSeason());

        $this->assertEquals($this->prevTable->getGames(), $table->getGames());
        $this->assertEquals($this->prevTable->getPoints(), $table->getPoints());
        $this->assertEquals($this->prevTable->getBoardPoints(), $table->getBoardPoints());
        $this->assertEquals($this->prevTable->getWins(), $table->getWins());
        $this->assertEquals($this->prevTable->getDraws(), $table->getDraws());
        $this->assertEquals($this->prevTable->getLosses(), $table->getLosses());
        $this->assertEquals($this->prevTable->getFirstBoardPoints(), $table->getFirstBoardPoints());
        $this->assertEquals($this->prevTable->getPosition(), $table->getPosition());
    }

    private function createRepo(): BundesligaTableRepository
    {
        $mock = $this->getMockBuilder(BundesligaTableRepository::class)->disableOriginalConstructor()->getMock();
        $mock->expects(static::any())->method('findTableByTeamAndMatchDay')->willReturn($this->prevTable);

        /* @var BundesligaTableRepository $mock */
        return $mock;
    }

    private function createResults(int $matchDay = 1): BundesligaResults
    {
        $mock = $this->getMockBuilder(BundesligaResults::class)->getMock();
        $mock->expects(static::any())->method('getSeason')->willReturn($this->season);
        $mock->expects(static::any())->method('getMatchDay')->willReturn($matchDay);

        /* @var BundesligaResults $mock */
        return $mock;
    }

    private function createTable(): BundesligaTable
    {
        $mock = $this->getMockBuilder(BundesligaTable::class)->getMock();
        $mock->expects(static::any())->method('getPoints')->willReturn(5);
        $mock->expects(static::any())->method('getBoardPoints')->willReturn(12);
        $mock->expects(static::any())->method('getGames')->willReturn(7);
        $mock->expects(static::any())->method('getWins')->willReturn(2);
        $mock->expects(static::any())->method('getDraws')->willReturn(1);
        $mock->expects(static::any())->method('getLosses')->willReturn(4);
        $mock->expects(static::any())->method('getFirstBoardPoints')->willReturn(3);
        $mock->expects(static::any())->method('getPosition')->willReturn(5);

        return $mock;
    }
}
