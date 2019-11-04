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
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaRelegation;
use App\Entity\Bundesliga\BundesligaRelegationMatch;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTeam;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaRelegationMatchFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(20, 'bl_relegation_match', function ($i) {
            $match = new BundesligaRelegationMatch();
            $match->setBoard($this->faker->numberBetween(1, 5));
            $match->setColor($this->faker->boolean() ? 'w' : 'b');
            $match->setResult($this->createResult());
            if ($this->faker->boolean(10)) {
                $match->setWinByDefault(true);
            }

            /** @var BundesligaSeason $season */
            $season = $this->getRandomReference(BundesligaSeason::class, 'bl_season');
            $match->setSeason($season);

            /** @var BundesligaPlayer $player */
            $player = $this->getRandomReference(BundesligaPlayer::class, 'bl_player');
            $match->setPlayer($player);

            /** @var BundesligaOpponent $opponent */
            $opponent = $this->getRandomReference(BundesligaOpponent::class, 'bl_opponent');
            $match->setOpponent($opponent);

            /** @var BundesligaTeam $team */
            $team = $this->getRandomReference(BundesligaTeam::class, 'bl_team');
            $match->setOpponentTeam($team);

            /** @var BundesligaRelegation $relegation */
            $relegation = $this->getRandomReference(BundesligaRelegation::class, 'bl_relegation');
            $match->setResults($relegation);

            return $match;
        });

        $manager->flush();
    }

    private function createResult(): string
    {
        $pointsHome = $this->faker->numberBetween(0, 2);
        switch ($pointsHome) {
            case 0:
                $result = '0:2';
                break;
            case 1:
                $result = '1:1';
                break;
            default:
                $result = '2:0';
        }

        return $result;
    }

    public function getDependencies()
    {
        return [
            BundesligaSeasonFixtures::class,
            BundesligaPlayerFixtures::class,
            BundesligaOpponentFixtures::class,
            BundesligaTeamFixtures::class,
            BundesligaRelegationFixtures::class,
        ];
    }
}
