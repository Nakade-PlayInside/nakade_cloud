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

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use App\Logger\GrabberLoggerTrait;
use App\Tools\DGoB\Model\ResultModel;
use App\Tools\DGoB\Model\SeasonModel;
use App\Tools\DGoB\Model\TeamModel;
use App\Tools\DGoB\SeasonCatcher;
use Doctrine\ORM\EntityManagerInterface;

class ArchiveTransfer extends AbstractTransfer
{
    use GrabberLoggerTrait;

    private $transferFactory;
    private $seasonCatcher;

    public function __construct(EntityManagerInterface $manager, TransferFactory $transferFactory, SeasonCatcher $seasonCatcher)
    {
        parent::__construct($manager);
        $this->transferFactory = $transferFactory;
        $this->seasonCatcher = $seasonCatcher;
    }

    /**
     * For a new season, you first have to create it.
     * Older seasons from the archive will be created but you have to provide the year span (eg 2019_2020) and the league.
     * DO NOT FORGET THE ACTUAL SEASON FLAG SET TO FALSE!
     */
    public function transfer(string $yearSpan, string $league, bool $isActualSeason = true): SeasonModel
    {
        //grabbing all data form DGoB Site
        $seasonModel = $this->seasonCatcher->extract($yearSpan, $league, $isActualSeason);
        $season = $this->getSeason($seasonModel);

        foreach ($seasonModel->results as $resultModel) {
            $home = $this->getTeam($resultModel->homeTeam);
            $season->addTeam($home);

            $away = $this->getTeam($resultModel->homeTeam);
            $season->addTeam($away);

            $result = $this->getResult($season, $home, $away, $resultModel);

            //no match details during actual season due to data mismatches on DGoB site
            if ($isActualSeason) {
                $this->logger->info('Actual season found. Skipping match data.');

                continue;
            }

            //match, player and opponent transfer for nakade games
            //used by the archive grabber
            if ($result->isNakadeResult()) {
                $this->getMatch($season, $result, $resultModel);
            }
        }
        $this->manager->flush();

        return $seasonModel;
    }

    private function getSeason(SeasonModel $seasonModel): BundesligaSeason
    {
        $transfer = $this->transferFactory->getTransfer(TransferFactory::SEASON_TRANSFER);
        if (!assert($transfer instanceof SeasonTransfer)) {
            throw new \LogicException('Expected type %s not found.', SeasonTransfer::class);
        }
        //creates new season only if new data found
        $season = $transfer->transfer($seasonModel);
        $this->manager->persist($season);

        return $season;
    }

    private function getTeam(TeamModel $teamModel): BundesligaTeam
    {
        $transfer = $this->transferFactory->getTransfer(TransferFactory::TEAM_TRANSFER);
        if (!assert($transfer instanceof TeamTransfer)) {
            throw new \LogicException('Expected type %s not found.', TeamTransfer::class);
        }
        //creates new team only if new data are found.
        $team = $transfer->transfer($teamModel);
        $this->manager->persist($team);

        return $team;
    }

    private function getResult(BundesligaSeason $season, BundesligaTeam $home, BundesligaTeam $away, ResultModel $model): BundesligaResults
    {
        $transfer = $this->transferFactory->getTransfer(TransferFactory::RESULT_TRANSFER);
        if (!assert($transfer instanceof ResultTransfer)) {
            throw new \LogicException('Expected type %s not found.', ResultTransfer::class);
        }
        //creates new team only if new data are found.
        $result = $transfer->transfer($season, $home, $away, $model);
        $this->manager->persist($result);

        return $result;
    }

    private function getMatch(BundesligaSeason $season, BundesligaResults $results, ResultModel $model): BundesligaResults
    {
        $transfer = $this->transferFactory->getTransfer(TransferFactory::MATCH_TRANSFER);
        if (!assert($transfer instanceof MatchTransfer)) {
            throw new \LogicException('Expected type %s not found.', MatchTransfer::class);
        }
        //creates new matches only if new data are found.
        $result = $transfer->transfer($season, $results, $model);
        //no need to persist since it is already done

        return $result;
    }
}
