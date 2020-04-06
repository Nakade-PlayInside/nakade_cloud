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
        $this->positioner = new TablePositioner();
    }

    public function testPositionSetting()
    {
        $tables = $this->generate(3);
        $this->positioner->create($tables);

        $first = array_shift($tables);
        $this->assertEquals(1, $first->getPosition());

        $last = array_pop($tables);
        $this->assertEquals(3, $last->getPosition());
    }

    public function testDefaultImgSetting()
    {
        $tables = $this->generate(3);
        $this->positioner->create($tables);

        $first = array_shift($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_SAME, $first->getImgSrc());

        $last = array_pop($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_SAME, $last->getImgSrc());
    }

    public function testImgSettingOnPositionChange()
    {
        $tables = $this->generate(3, 2);
        $this->positioner->create($tables);

        $first = array_shift($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_UP, $first->getImgSrc());

        $last = array_pop($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_DOWN, $last->getImgSrc());

        $middle = array_shift($tables);
        $this->assertEquals(BundesligaTable::IMG_POS_SAME, $middle->getImgSrc());
    }

    /**
     * @return BundesligaTable[]
     */
    private function generate(int $count, int $prevPosition = null): array
    {
        $tables = [];
        for ($i = 0; $i < $count; ++$i) {
            $table = new BundesligaTable();
            if ($prevPosition) {
                $table->setPosition($prevPosition);
            }
            $tables[] = $table;
        }

        return $tables;
    }
}
