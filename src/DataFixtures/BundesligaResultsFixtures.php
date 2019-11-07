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

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaOpponent;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaResultsFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        for ($count = 0; $count < BundesligaSeasonFixtures::COUNT; ++$count) {
            $season = $this->getSeason($count);
            $allTeams = $season->getTeams();
            $matchDay = 1;

            foreach ($allTeams as $team) {
                if ('Nakade' === $team->getName()) {
                    continue;
                }

                $result = new BundesligaResults();

                $result->setMatchDay($matchDay);
                $result->setPlayedAt($this->faker->dateTimeThisDecade);
                $result->setSeason($season);

                $home = $this->getReference('bl_team_nakade');
                $away = $team;
                if (0 === $matchDay % 2) {
                    $away = $home;
                    $home = $team;
                }
                $result->setHome($home);
                $result->setAway($away);
                $points = $this->createMatches($season, $team, $result);

                if ('Nakade' === $result->getHome()->getName()) {
                    $result->setBoardPointsHome($points);
                    $result->setBoardPointsAway(8 - $points);
                } else {
                    $result->setBoardPointsAway($points);
                    $result->setBoardPointsHome(8 - $points);
                }

                $manager->persist($result);
                $this->addReference(sprintf('bl_results_%d_%d', $count, $matchDay), $result);

                ++$matchDay;
            }
        }

        $manager->flush();
    }

    private function createMatches(BundesligaSeason $season, BundesligaTeam $team, BundesligaResults $results): int
    {
        $players = $season->getLineup()->getPlayers();
        shuffle($players);
        $points = 0;

        for ($i = 1; $i <= 4; ++$i) {
            $match = new BundesligaMatch();
            $match->setBoard($i);
            $match->setColor($this->faker->boolean() ? 'w' : 'b');
            $pointsHome = $this->faker->numberBetween(0, 2);

            $match->setResult($this->createResult($pointsHome));
            if ($pointsHome === 2 && $this->faker->boolean()) {
                $match->setWinByDefault(true);
            }
            $match->setPlayer(array_shift($players));
            $match->setSeason($season);

            /** @var BundesligaOpponent $opponent */
            $opponent = $this->getRandomReference(BundesligaOpponent::class, 'bl_opponent');
            $match->setOpponent($opponent);
            $match->setResults($results);
            //for correct result
            $points += $pointsHome;
            $this->getManager()->persist($match);
        }

        return $points;
    }

    private function getSeason($count): BundesligaSeason
    {
        $name = BundesligaSeasonFixtures::GROUP_NAME.'_'.$count;
        if (!$this->hasReference($name)) {
            throw new \LogicException(sprintf('Expected reference "%s" not found.', $name));
        }

        return $this->getReference($name);
    }

    public function getDependencies()
    {
        return [
            BundesligaSeasonFixtures::class,
            BundesligaLineupFixtures::class,
            BundesligaOpponentFixtures::class,
            BundesligaTeamFixtures::class,
        ];
    }
}
