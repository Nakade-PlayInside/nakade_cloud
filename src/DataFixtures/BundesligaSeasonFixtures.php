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

namespace App\DataFixtures;

use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaSeasonFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(8, 'bl_season', function ($i) {
            $startYear = 10 + $i;
            $title = sprintf('Saison 20%d/%d', $startYear, $startYear + 1);
            $startDate = sprintf('20%d-08-%d', $startYear, $this->faker->numberBetween(1, 28));
            $endDate = sprintf('20%d-05-%d', $startYear + 1, $this->faker->numberBetween(1, 28));

            $season = new BundesligaSeason();
            $season->setTitle($title);
            $season->setStartAt(new \DateTime($startDate));
            $season->setEndAt(new \DateTime($endDate));

            /** @var BundesligaTeam $team */
            $team = $this->getReference('bl_team_1');
            $season->addTeam($team);

            while (sizeof($season->getTeams()) < 8) {
                /** @var BundesligaTeam $team */
                $team = $this->getRandomReference(BundesligaTeam::class, 'bl_team');
                $season->addTeam($team);
            }

            while (sizeof($season->getPlayers()) < 10) {
                /** @var BundesligaPlayer $player */
                $player = $this->getRandomReference(BundesligaPlayer::class, 'bl_player');
                $season->addPlayer($player);
            }

            return $season;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            BundesligaTeamFixtures::class,
            BundesligaPlayerFixtures::class,
        ];
    }
}