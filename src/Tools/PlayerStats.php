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

namespace App\Tools;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Repository\Bundesliga\BundesligaMatchRepository;
use App\Tools\Model\PlayerStatsModel;

/**
 * Create player stats by season.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class PlayerStats
{
    const MATCH_COLOR_WHITE = 'w';
    const MATCH_WIN = '2:0';
    const MATCH_DRAW = '1:1';
    const MATCH_LOSS = '0:2';

    private $repository;

    public function __construct(BundesligaMatchRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getStats(BundesligaSeason $season, BundesligaPlayer $player): ?PlayerStatsModel
    {
        $matches = $this->repository->findPlayerMatches($season, $player);
        $model = new PlayerStatsModel($season, $player, $matches);

        foreach ($matches as $match) {
            $this->makeColorStats($model, $match);
            $this->makeBoardStats($model, $match);
        }

        return $model;
    }

    private function makeColorStats(PlayerStatsModel $model, BundesligaMatch $match)
    {
        self::MATCH_COLOR_WHITE === $match->getColor() ? $model->white++ : $model->black++;

        $method = 'blackPoints';
        if (self::MATCH_COLOR_WHITE === $match->getColor()) {
            $method = 'whitePoints';
        }

        if (!property_exists(PlayerStatsModel::class, $method)) {
            throw new \LogicException(sprintf('The property "%s" is not found in <%s>!', $method, PlayerStatsModel::class));
        }

        switch ($match->getResult()) {
            case self::MATCH_WIN:
                $model->$method += 2;
                ++$model->wins;
                $model->points += 2;
                ++$model->games;
                break;
            case self::MATCH_DRAW:
                ++$model->$method;
                ++$model->draws;
                ++$model->points;
                ++$model->games;
                break;
            case self::MATCH_LOSS:
                $model->losses++;
                ++$model->games;
                break;
        }
    }

    private function makeBoardStats(PlayerStatsModel $model, BundesligaMatch $match)
    {
        switch ($match->getBoard()) {
            case 1:
                $model->firstBoard++;
                $method = 'firstBoardPoints';
                break;
            case 2:
                $model->secondBoard++;
                $method = 'secondBoardPoints';
                break;
            case 3:
                $model->thirdBoard++;
                $method = 'thirdBoardPoints';
                break;
            case 4:
                $model->fourthBoard++;
                $method = 'fourthBoardPoints';
                break;
        }

        if (!property_exists(PlayerStatsModel::class, $method)) {
            throw new \LogicException(sprintf('The property "%s" is not found in <%s>!', $method, PlayerStatsModel::class));
        }

        if (self::MATCH_WIN === $match->getResult()) {
            $model->$method += 2;
        }
        if (self::MATCH_DRAW === $match->getResult()) {
            ++$model->$method;
        }
    }
}
