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
use App\Tools\Model\TableStatsModel;
use App\Tools\TableStats;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class BundesligaTableFixtures extends BaseFixture implements DependentFixtureInterface
{
    private $stats;

    public function __construct(TableStats $stats)
    {
        $this->stats = $stats;
    }

    protected function loadData(ObjectManager $manager)
    {
        $allSeasons = $this->getReferencesKeysByGroup(BundesligaSeason::class, 'bl_season');

        /* @var BundesligaSeason $season */
        foreach ($allSeasons as $seasonKey) {
            $season = $this->getReference($seasonKey);
            for ($matchDay = 1; $matchDay < 10; ++$matchDay) {
                $matchResults = $this->stats->getStats($season, $matchDay);
                $this->createTable($manager, $season, $matchResults, (string) $matchDay);
            }
        }

        $manager->flush();
    }

    private function createTable(ObjectManager $manager, BundesligaSeason $season, array $matchResults, string $matchDay)
    {
        $position = 1;
        while (!empty($matchResults)) {
            /** @var TableStatsModel $model */
            $model = array_shift($matchResults);
            $table = new BundesligaTable();

            $table->setPoints((string) $model->points)
                ->setPosition($position)
                ->setWins((string) $model->wins)
                ->setDraws((string) $model->draws)
                ->setLosses((string) $model->losses)
                ->setGames((string) $model->games)
                ->setBoardPoints((string) $model->boardPoints)
                ->setBundesligaTeam($model->team)
                ->setMatchDay($matchDay)
                ->setBundesligaSeason($season)
                ->setImgSrc('http://www.dgob.de/lmo/img/lmo-tab0.gif')
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
    }

    public function getDependencies()
    {
        return [
            BundesligaResultsFixtures::class,
        ];
    }
}
