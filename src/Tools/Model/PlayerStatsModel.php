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

namespace App\Tools\Model;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaSeason;

class PlayerStatsModel
{
    public $wins = 0;
    public $draws = 0;
    public $losses = 0;
    public $points = 0;
    public $games = 0;
    public $white = 0;
    public $whitePoints = 0;
    public $black = 0;
    public $blackPoints = 0;
    public $firstBoard = 0;
    public $secondBoard = 0;
    public $thirdBoard = 0;
    public $fourthBoard = 0;
    public $firstBoardPoints = 0;
    public $secondBoardPoints = 0;
    public $thirdBoardPoints = 0;
    public $fourthBoardPoints = 0;
    public $season;
    public $player;
    /**
     * @var BundesligaMatch[]
     */
    public $matches;

    public function __construct(BundesligaSeason $season, BundesligaPlayer $player, array $matches) {
        $this->player = $player;
        $this->matches = $matches;
        $this->season = $season;
    }

    public function getWinPercentage(): ?int
    {
        if ($this->games === 0) {
            return null;
        }

        return intval(100 * $this->points / ($this->games * 2));
    }
}
