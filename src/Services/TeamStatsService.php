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

namespace App\Services;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Tools\Model\TeamStatsModel;
use App\Tools\PlayerStats;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Create season stats of team players.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class TeamStatsService
{
    private $service;
    private $manager;

    public function __construct(EntityManagerInterface $manager, PlayerStats $service)
    {
        $this->service = $service;
        $this->manager = $manager;
    }

    public function getStats(int $seasonId = null): ?TeamStatsModel
    {
        $season = $this->findSeason($seasonId);
        if (!$season) {
            return null;
        }
        $teamPlayers = $season->getLineup()->getPlayers();

        $teamStats = new TeamStatsModel($season);
        foreach ($teamPlayers as $player) {
            $model = $this->service->getStats($season, $player);
            if ($model) {
                $teamStats->addPlayer($model);
            }
        }
        $lastMatchDay = $this->findLastMatchDay($season);
        if (!$lastMatchDay) {
            $found = $this->manager->getRepository(BundesligaResults::class)->findLastMatchDay($season);
            $lastMatchDay = $found ? $found : '9';
        }

        $teamStats->setLastMatchDay($lastMatchDay);

        return $teamStats;
    }

    private function findSeason(int $seasonId = null): ?BundesligaSeason
    {
        if ($seasonId) {
            return $this->manager->getRepository(BundesligaSeason::class)->find($seasonId);
        }

        return $this->manager->getRepository(BundesligaSeason::class)->findOneBy(['actualSeason' => true]);
    }

    private function findLastMatchDay(BundesligaSeason $season): ?string
    {
        if ($season->isActualSeason()) {
            return $this->manager->getRepository(BundesligaResults::class)->findMatchDayUnplayed($season);
        }

        return null;
    }
}
