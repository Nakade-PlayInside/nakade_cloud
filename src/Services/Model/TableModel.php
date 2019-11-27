<?php

declare(strict_types=1);
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2019 Dr. Holger Maerz
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

namespace App\Services\Model;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;

class TableModel
{
    private $matchDayRange;
    private $actualTable;
    private $season;
    private $matchDay;
    private $lastMatchDay;
    private $result;
    private $nextResult;

    public function __construct(BundesligaSeason $season, int $matchDay, string $lastMatchDay)
    {
        $this->season = $season;
        $this->matchDay = $matchDay;
        $this->lastMatchDay = $lastMatchDay;
    }

    public function getSeason(): BundesligaSeason
    {
        return $this->season;
    }

    public function getMatchDay(): int
    {
        return $this->matchDay;
    }

    public function getLastMatchDay(): string
    {
        return $this->lastMatchDay;
    }

    public function getTitle(): string
    {
        $title = $this->season->getTitle();
        $pattern = '#.*(\d{4}\/\d{2})#';
        if (1 === preg_match($pattern, $title, $matches)) {
            $title = $matches[1];
        }
        return sprintf("%s. Bundesliga %s", intval($this->season->getLeague()), $title);
    }

    public function getMatchDayRange(): array
    {
        return $this->matchDayRange;
    }

    public function setMatchDayRange(array $matchDayRange): self
    {
        $this->matchDayRange = $matchDayRange;

        return $this;
    }

    /**
     * @return BundesligaTable[]
     */
    public function getActualTable(): array
    {
        return $this->actualTable;
    }

    public function setActualTable(array $actualTable): self
    {
        $this->actualTable = $actualTable;

        return $this;
    }

    public function getResult(): ?BundesligaResults
    {
        return $this->result;
    }

    public function setResult(BundesligaResults $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getNextResult(): ?BundesligaResults
    {
        return $this->nextResult;
    }

    public function setNextResult(BundesligaResults $nextResult): self
    {
        $this->nextResult = $nextResult;

        return $this;
    }

}
