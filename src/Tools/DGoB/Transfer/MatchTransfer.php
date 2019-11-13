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

namespace App\Tools\DGoB\Transfer;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaOpponent;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Tools\DGoB\Model\MatchModel;
use App\Tools\DGoB\Model\ResultModel;
use App\Tools\DGoB\Model\TeamModel;
use App\Tools\DGoB\Transfer\Helper\ColorHelper;
use App\Tools\DGoB\Transfer\Helper\ResultHelper;
use Doctrine\ORM\EntityManagerInterface;

class MatchTransfer extends AbstractTransfer
{
    private $playerTransfer;
    private $opponentTransfer;

    public function __construct(EntityManagerInterface $manager, PlayerTransfer $playerTransfer, OpponentTransfer $opponentTransfer)
    {
        parent::__construct($manager);
        $this->playerTransfer = $playerTransfer;
        $this->opponentTransfer = $opponentTransfer;
    }

    public function transfer(BundesligaSeason $season, ResultModel $model, BundesligaResults $results)
    {
        foreach ($model->matches as $matchModel) {
            if (!$matchModel->getHomePlayer() || !$matchModel->getAwayPlayer()) {
                continue;
            }
            $player = $this->getPlayer($model, $matchModel);
            $opponent = $this->getOpponent($model, $matchModel);

            $match = $this->manager->getRepository(BundesligaMatch::class)->findOneBy(
                [
                    'season' => $season,
                    'player' => $player,
                    'opponent' => $opponent,
                ]
            );

            if (!$match) {
                $result = ResultHelper::getResult($model->homeTeam->name, $matchModel->result);
                $color = ColorHelper::getColor($model->homeTeam->name, $matchModel->getColor());

                $match = new BundesligaMatch();
                $match->setPlayer($player)
                        ->setOpponent($opponent)
                        ->setSeason($season)
                        ->setBoard($matchModel->board)
                        ->setColor($color)
                        ->setResult($result)
                ;

                $this->manager->persist($match);
            }

            $match->setResults($results);
            $this->manager->flush();
        }
    }

    private function getOpponent(ResultModel $resultModel, MatchModel $matchModel): BundesligaOpponent
    {
        if ($this->isNakadeMatch($resultModel->homeTeam)) {
            return $this->opponentTransfer->transfer($matchModel->getAwayPlayer());
        }

        return $this->opponentTransfer->transfer($matchModel->getHomePlayer());
    }

    private function getPlayer(ResultModel $resultModel, MatchModel $matchModel): BundesligaPlayer
    {
        if ($this->isNakadeMatch($resultModel->homeTeam)) {
            return $this->playerTransfer->transfer($matchModel->getHomePlayer());
        }

        return $this->playerTransfer->transfer($matchModel->getAwayPlayer());
    }

    private function isNakadeMatch(TeamModel $team): bool
    {
        return false !== stripos($team->name, self::HOME_TEAM);
    }
}
