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

use App\Entity\Bundesliga\BundesligaTable;
use App\Repository\Bundesliga\BundesligaTableRepository;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TablePositioner
{
    private $repos;

    public function __construct(BundesligaTableRepository $repos)
    {
        $this->repos = $repos;
    }

    public function create(array $tables): void
    {
        /** @var BundesligaTable[] $tables */
        $position = 1;
        foreach ($tables as $table) {
            //no prev position on first match day
            if (!empty($table->getPosition())) {
                $prevPosition = $this->findPreviousPosition($table);
                $img = $this->createImg($position, $prevPosition);
                $table->setImgSrc($img);
            }
            $table->setPosition($position);
            ++$position;
        }
    }

    private function findPreviousPosition(BundesligaTable $table): int
    {
        $prevMatchDay = $table->getMatchDay() - 1;
        $table = $this->repos->findTableByTeamAndMatchDay($table->getBundesligaSeason(), $table->getBundesligaTeam(), $prevMatchDay);

        return $table->getPosition();
    }

    private function createImg(int $newPosition, int $prevPosition): string
    {
        if ($newPosition > $prevPosition) {
            return BundesligaTable::IMG_POS_DOWN;
        }
        if ($newPosition < $prevPosition) {
            return BundesligaTable::IMG_POS_UP;
        }

        return BundesligaTable::IMG_POS_SAME;
    }
}
