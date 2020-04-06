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
use App\Entity\Bundesliga\BundesligaTable;
use App\Entity\Bundesliga\BundesligaTeam;
use App\Services\UpdateTableLogic\TableStatsGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TableStatsGeneratorTest extends TestCase
{
    private $statsGenerator = null;
    private $homeTeam;
    private $awayTeam;
    private $home;
    private $away;

    public function setUp(): void
    {
        $this->homeTeam = new BundesligaTeam();
        $this->homeTeam->setName('Home');

        $this->awayTeam = new BundesligaTeam();
        $this->awayTeam->setName('Away');

        $this->home = (new BundesligaTable())->setBundesligaTeam($this->homeTeam);
        $this->away = (new BundesligaTable())->setBundesligaTeam($this->awayTeam);

        $this->statsGenerator = new TableStatsGenerator();
    }

    /**
     * @expectedException \LogicException
     */
    public function testException()
    {
        $results = $this->createResults(0, 0);
        $this->statsGenerator->create($this->away, $this->home, $results);
    }

    public function testIsNotPlayed()
    {
        $results = $this->createResults(0,0);
        $this->statsGenerator->create($this->home, $this->away, $results);

        $this->assertEquals(0, $this->home->getGames());
        $this->assertEquals(0, $this->away->getGames());
    }

    public function testStatsOnDraw()
    {
        $results = $this->createResults(4,4);
        $this->statsGenerator->create($this->home, $this->away, $results);

        $this->assertEquals(1, $this->home->getGames());
        $this->assertEquals(1, $this->away->getGames());
        $this->assertEquals(0, $this->home->getWins());
        $this->assertEquals(0, $this->away->getWins());
        $this->assertEquals(1, $this->home->getDraws());
        $this->assertEquals(1, $this->away->getDraws());
        $this->assertEquals(0, $this->home->getLosses());
        $this->assertEquals(0, $this->away->getLosses());
        $this->assertEquals(1, $this->home->getPoints());
        $this->assertEquals(1, $this->away->getPoints());
        $this->assertEquals(4, $this->home->getBoardPoints());
        $this->assertEquals(4, $this->away->getBoardPoints());
    }

    public function testStatsOnWin()
    {
        $results = $this->createResults(6,2);
        $this->statsGenerator->create($this->home, $this->away, $results);

        $this->assertEquals(1, $this->home->getGames());
        $this->assertEquals(1, $this->away->getGames());
        $this->assertEquals(1, $this->home->getWins());
        $this->assertEquals(0, $this->away->getWins());
        $this->assertEquals(0, $this->home->getDraws());
        $this->assertEquals(0, $this->away->getDraws());
        $this->assertEquals(0, $this->home->getLosses());
        $this->assertEquals(1, $this->away->getLosses());
        $this->assertEquals(2, $this->home->getPoints());
        $this->assertEquals(0, $this->away->getPoints());
        $this->assertEquals(6, $this->home->getBoardPoints());
        $this->assertEquals(2, $this->away->getBoardPoints());
    }

    public function testStatsOnLoss()
    {
        $results = $this->createResults(2,4);
        $this->statsGenerator->create($this->home, $this->away, $results);

        $this->assertEquals(1, $this->home->getGames());
        $this->assertEquals(1, $this->away->getGames());
        $this->assertEquals(0, $this->home->getWins());
        $this->assertEquals(1, $this->away->getWins());
        $this->assertEquals(0, $this->home->getDraws());
        $this->assertEquals(0, $this->away->getDraws());
        $this->assertEquals(1, $this->home->getLosses());
        $this->assertEquals(0, $this->away->getLosses());
        $this->assertEquals(0, $this->home->getPoints());
        $this->assertEquals(2, $this->away->getPoints());
        $this->assertEquals(2, $this->home->getBoardPoints());
        $this->assertEquals(4, $this->away->getBoardPoints());
    }

    private function createResults(int $boardPointsHome = 0, int $boardPointsAway = 0): BundesligaResults
    {
        $mock = $this->getMockBuilder(BundesligaResults::class)->getMock();
        $mock->expects(static::any())->method('getHome')->willReturn($this->homeTeam);
        $mock->expects(static::any())->method('getAway')->willReturn($this->awayTeam);
        $mock->expects(static::any())->method('getBoardPointsHome')->willReturn($boardPointsHome);
        $mock->expects(static::any())->method('getBoardPointsAway')->willReturn($boardPointsAway);

        /* @var BundesligaResults $mock */
        return $mock;
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
