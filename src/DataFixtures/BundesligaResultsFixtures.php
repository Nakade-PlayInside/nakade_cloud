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

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Tools\HarmonicPairing;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaResultsFixtures extends BaseFixture implements DependentFixtureInterface
{
    private $pairing;

    public function __construct(HarmonicPairing $pairing)
    {
        $this->pairing = $pairing;
    }

    protected function loadData(ObjectManager $manager)
    {
        $allSeasons = $this->getReferencesKeysByGroup(BundesligaSeason::class, 'bl_season');
        $count = 0;

        /* @var BundesligaSeason $season */
        foreach ($allSeasons as $seasonKey) {
            $season = $this->getReference($seasonKey);
            $allPairings = $this->pairing->getPairings($season->getTeams()->toArray());
            foreach ($allPairings as $matchDay => $matches) {
                $this->createMatches($manager, $matchDay, $season, $matches, $count);
            }
            ++$count;
        }

        $manager->flush();
    }

    private function createMatches(ObjectManager $manager, int $matchDay, BundesligaSeason $season, array $matches, int $seasonCount)
    {
        $count = 0;
        foreach ($matches as $match) {
            $results = new BundesligaResults();
            $results->setSeason($season);
            $results->setMatchDay((int) $matchDay);
            $results->setHome($match[0]);
            $results->setAway($match[1]);

            if ($season->getStartAt()) {
                $playDate = clone $season->getStartAt();
                $dateTime = \DateTime::createFromFormat('Y-m-d', $playDate->format('Y-m-d'));
                $days = sprintf('+%d days', ($matchDay * 30) + 3);
                $results->setPlayedAt($dateTime->modify($days));
            }

            $homePoints = $this->faker->numberBetween(0, 8);
            $awayPoints = 8 - $homePoints;
            $results->setBoardPointsHome($homePoints);
            $results->setBoardPointsAway($awayPoints);

            if ($season->isActualSeason() && $matchDay > 3) {
                $results->setBoardPointsHome(0);
                $results->setBoardPointsAway(0);
            }

            $manager->persist($results);
            $this->addReference(sprintf('bl_results_%d_%d_%d', $seasonCount, $matchDay, $count), $results);
            ++$count;
        }
    }

    public function getDependencies()
    {
        return [
            BundesligaSeasonFixtures::class,
        ];
    }
}
