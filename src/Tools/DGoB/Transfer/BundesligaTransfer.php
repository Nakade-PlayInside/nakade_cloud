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

use App\Entity\Bundesliga\BundesligaTeam;
use App\Tools\DGoB\Model\SeasonModel;
use App\Tools\DGoB\SeasonCatcher;
use Doctrine\ORM\EntityManagerInterface;

class BundesligaTransfer extends AbstractTransfer
{
    private $transferFactory;

    public function __construct(EntityManagerInterface $manager, TransferFactory $transferFactory)
    {
        parent::__construct($manager);
        $this->transferFactory = $transferFactory;
    }

    public function transfer(string $yearSpan, string $league, bool $actualSeason = true): SeasonModel
    {
        $seasonCatcher = new SeasonCatcher($yearSpan, $league, $actualSeason);
        $seasonModel = $seasonCatcher->extract();

        $season = $this->transferFactory->getTransfer(TransferFactory::SEASON_TRANSFER)->transfer($seasonModel);
        foreach ($seasonModel->results as $resultModel) {
            $home = $this->transferFactory->getTransfer(TransferFactory::TEAM_TRANSFER)->transfer($resultModel->homeTeam);
            $away = $this->transferFactory->getTransfer(TransferFactory::TEAM_TRANSFER)->transfer($resultModel->awayTeam);
            $result = $this->transferFactory->getTransfer(TransferFactory::RESULT_TRANSFER)->transfer($season, $resultModel, $home, $away);

            //no match details during actual season due to data mismatches on DGoB site
            if ($actualSeason) {
                continue;
            }

            //match, player and opponent transfer
            if ($this->isNakadeMatch($home) || $this->isNakadeMatch($away)) {
                $this->transferFactory->getTransfer(TransferFactory::MATCH_TRANSFER)->transfer($season, $resultModel, $result);
            }
        }

        return $seasonModel;
    }

    private function isNakadeMatch(BundesligaTeam $team): bool
    {
        return false !== stripos($team->getName(), self::HOME_TEAM);
    }
}
