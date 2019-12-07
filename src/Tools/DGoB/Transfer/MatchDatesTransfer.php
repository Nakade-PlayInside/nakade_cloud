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

class MatchDatesTransfer extends AbstractTransfer
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
     * If a.
     * DO NOT FORGET THE ACTUAL SEASON FLAG SET TO FALSE!
     */
    public function transfer(string $yearSpan, string $league, bool $isActualSeason = true): ?SeasonModel
    {
        //grabbing all data form DGoB Site
        $seasonModel = $this->seasonCatcher->extract($yearSpan, $league, $isActualSeason);
        $season = $this->getSeason($seasonModel);
        if (count($season->getTeams()) > 0) {
            $this->logger->notice('Season has already registered teams. Skipping Transfer.');

            return null;
        }

        foreach ($seasonModel->results as $resultModel) {
            $home = $this->getTeam($resultModel->homeTeam);
            $season->addTeam($home);

            $away = $this->getTeam($resultModel->homeTeam);
            $season->addTeam($away);

            $this->getResult($season, $home, $away, $resultModel);
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
}
