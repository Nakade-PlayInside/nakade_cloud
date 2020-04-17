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
use App\Entity\Bundesliga\BundesligaTeam;
use App\Repository\Bundesliga\BundesligaTableRepository;
use App\Services\UpdateTableLogic\TablePositioner;
use PHPUnit\Framework\TestCase;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TablePositionerTest extends TestCase
{
    private $positioner = null;

    public function setUp(): void
    {
        $mock = $this->createRepo();
        $this->positioner = new TablePositioner($mock);
    }

    public function testPositionSetting()
    {
        $tables = $this->generateTables();
        $this->positioner->create($tables);

        $i = 1;
        /** @var BundesligaTable $table */
        foreach ($tables as $table) {
            $this->assertEquals($i++, $table->getPosition());
        }
    }

    public function testDefaultImgSetting()
    {
        $tables = $this->generateTables();
        $this->positioner->create($tables);

        /** @var BundesligaTable $table */
        foreach ($tables as $table) {
            $this->assertEquals(BundesligaTable::IMG_POS_SAME, $table->getImgSrc());
        }
    }

    public function testImgSettingOnPositionChange()
    {
        $table = (new BundesligaTable())->setPosition(2);

        $mock = $this->createRepo($table);
        $positioner = new TablePositioner($mock);

        $tables = $this->generateTables(2);
        $positioner->create($tables);

        $first = array_shift($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_UP, $first->getImgSrc());

        $second = array_shift($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_SAME, $second->getImgSrc());

        $last = array_pop($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_DOWN, $last->getImgSrc());
    }

    private function createRepo(BundesligaTable $table = null): BundesligaTableRepository
    {
        $mock = $this->getMockBuilder(BundesligaTableRepository::class)->disableOriginalConstructor()->getMock();
        $mock->expects(static::any())->method('findTableByTeamAndMatchDay')->willReturn($table);

        /* @var BundesligaTableRepository $mock */
        return $mock;
    }

    private function createSeason(): BundesligaSeason
    {
        /** @var BundesligaSeason $mock */
        $mock = $this->getMockBuilder(BundesligaSeason::class)->disableOriginalConstructor()->getMock();
        return $mock;
    }

    private function createTeam(): BundesligaTeam
    {
        /** @var BundesligaTeam $mock */
        $mock = $this->getMockBuilder(BundesligaTeam::class)->disableOriginalConstructor()->getMock();
        return $mock;
    }

    /**
     * @return BundesligaTable[]
     */
    private function generateTables(int $prevPosition = null): array
    {
        $tables = [];
        for ($i = 0; $i < 8; ++$i) {
            $table = new BundesligaTable();
            if ($prevPosition) {
                $table->setPosition($prevPosition);
                $table->setBundesligaSeason($this->createSeason());
                $table->setBundesligaTeam($this->createTeam());
            }
            $tables[] = $table;
        }

        return $tables;
    }
}
