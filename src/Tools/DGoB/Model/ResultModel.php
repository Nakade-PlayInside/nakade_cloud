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

namespace App\Tools\DGoB\Model;

class ResultModel
{
    /** @var MatchModel[] */
    public $matches = [];
    public $boardPointsHome;
    public $boardPointsAway;
    public $date;

    /** @var TeamModel */
    public $homeTeam;

    /** @var TeamModel */
    public $awayTeam;
    public $matchDay;
    public $rawKgsId;

    public function __construct(TeamModel $homeTeam, TeamModel $awayTeam, string $date)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->date = $date;
    }

    public function getMatchDay(): int
    {
        return (int) $this->matchDay;
    }

    public function getBoardPointsHome(): int
    {
        if (!$this->boardPointsHome) {
            return 0;
        }

        $this->boardPointsHome = trim($this->boardPointsHome);
        if ('_' === $this->boardPointsHome) {
            $this->boardPointsHome = 0;
        }

        return (int) $this->boardPointsHome;
    }

    public function getBoardPointsAway(): int
    {
        if (!$this->boardPointsAway) {
            return 0;
        }

        $this->boardPointsAway = trim($this->boardPointsAway);
        if ('_' === $this->boardPointsAway) {
            $this->boardPointsAway = 0;
        }

        return (int) $this->boardPointsAway;
    }

    public function getDate(): ?\DateTimeInterface
    {
        if (!$this->date) {
            return null;
        }

        return \DateTime::createFromFormat('d.m.Y H:i', $this->date);
    }
}
