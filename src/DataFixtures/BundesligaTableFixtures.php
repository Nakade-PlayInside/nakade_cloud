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

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaTableFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        /** @var BundesligaSeason $season */
        $season = $this->getReference('bl_season_7');
        $season->setActualSeason(true);

        $matchDay = 1;
        $position = 1;
        foreach ($season->getTeams() as $team) {
            $table = new BundesligaTable();
            $boardPoints = $matchDay * 4;
            $table->setPosition($position)
                ->setLeague($season->getLeague())
                ->setMatchDay((string) $matchDay)
                ->setTeam($team->getName())
                ->setGames((string) $matchDay)
                ->setWins('0')
                ->setDraws((string) $matchDay)
                ->setLosses('0')
                ->setBoardPoints((string) $boardPoints)
                ->setImgSrc('http://www.dgob.de/lmo/img/lmo-tab0.gif')
                ->setSeason($season->getDGoBIndex())
                ->setPoints((string) $matchDay)
            ;

            if ($position < 3) {
                $table->setTendency(BundesligaTable::TENDENCY_AUFSTEIGER);
            } elseif (8 === $position) {
                $table->setTendency(BundesligaTable::TENDENCY_RELEGATION);
            } elseif ($position > 8) {
                $table->setTendency(BundesligaTable::TENDENCY_ABSTEIGER);
            }

            $manager->persist($table);
            ++$position;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            BundesligaSeasonFixtures::class,
        ];
    }
}
