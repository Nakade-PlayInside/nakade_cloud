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

use App\Logger\GrabberLoggerTrait;
use Doctrine\ORM\EntityManagerInterface;

class TransferFactory
{
    use GrabberLoggerTrait;

    const MATCH_TRANSFER = 10;
    const OPPONENT_TRANSFER = 20;
    const PLAYER_TRANSFER = 30;
    const RESULT_TRANSFER = 40;
    const SEASON_TRANSFER = 50;
    const TEAM_TRANSFER = 60;

    private $transferCollection = [];
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getTransfer(int $type)
    {
        if (empty($this->transferCollection[$type])) {
            $this->transferCollection[$type] = $this->create($type);
        }

        return $this->transferCollection[$type];
    }

    private function create($type)
    {
        switch ($type) {
            case self::MATCH_TRANSFER:
                $playerTransfer = $this->create(self::PLAYER_TRANSFER);
                $opponentTransfer = $this->create(self::OPPONENT_TRANSFER);
                $transfer = new MatchTransfer($this->manager, $playerTransfer, $opponentTransfer);
                break;
            case self::OPPONENT_TRANSFER:
                $transfer = new OpponentTransfer($this->manager);
                break;
            case self::PLAYER_TRANSFER:
                $transfer = new PlayerTransfer($this->manager);
                break;
            case self::RESULT_TRANSFER:
                $transfer = new ResultTransfer($this->manager);
                break;
            case self::SEASON_TRANSFER:
                $transfer = new SeasonTransfer($this->manager);
                break;
            case self::TEAM_TRANSFER:
                $transfer = new TeamTransfer($this->manager);
                break;
            default:
                $msg = sprintf('Type not found: "%s"', $type);
                throw new \InvalidArgumentException($msg);
        }
        $transfer->setLogger($this->logger);

        return $transfer;
    }
}
