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
use App\Repository\Bundesliga\BundesligaResultsRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaMatchFixtures extends BaseFixture implements DependentFixtureInterface
{
    private $repository;

    public function __construct(BundesligaResultsRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function loadData(ObjectManager $manager)
    {
        $allSeasons = $this->getReferencesKeysByGroup(BundesligaSeason::class, 'bl_season');

        /* @var BundesligaSeason $season */
        foreach ($allSeasons as $seasonKey) {
            $season = $this->getReference($seasonKey);
            $results = $this->repository->findNakadeResultsBySeason($season);
            foreach ($results as $matchDayResult) {
                if ($matchDayResult->getResult() === '0 : 0') {
                    continue;
                }
                $this->createMatches($manager, $matchDayResult);
            }
        }

        $manager->flush();
    }

    private function createMatches(ObjectManager $manager, BundesligaResults $results)
    {
        $season = $results->getSeason();
        $isHome = false !== stripos($results->getHome()->getName(), 'Nakade');
        $availablePlayers = $season->getLineup()->getPlayers();
        shuffle($availablePlayers);
        $colors = $this->getColors($isHome);
        $boardPoints = $isHome ? $results->getBoardPointsHome() : $results->getBoardPointsAway();

        //make four matches
        for ($board = 1; $board < 5; ++$board) {
            /** @var BundesligaOpponent $opponent */
            $opponent = $this->getRandomReference(BundesligaOpponent::class, 'bl_opponent');
            if (0 === $boardPoints) {
                $points = '0:2';
            } elseif ($boardPoints >= 2) {
                $boardPoints -= 2;
                $points = '2:0';
            } else {
                --$boardPoints;
                $points = '1:1';
            }

            $match = new BundesligaMatch();
            $match->setSeason($season)
                ->setBoard($board)
                ->setPlayer(array_shift($availablePlayers))
                ->setColor(array_shift($colors))
                ->setOpponent($opponent)
                ->setResult($points)
                ->setResults($results)
            ;

            $manager->persist($match);
        }
    }

    private function getColors(bool $isHome)
    {
        $color = ['w', 'b', 'w', 'b'];
        if ($isHome) {
            $color = array_reverse($color);
        }

        return $color;
    }

    public function getDependencies()
    {
        return [
            BundesligaResultsFixtures::class,
        ];
    }
}
