<?php
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

namespace App\Controller;

use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Tools\Bundesliga\TableCatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BundesligaController extends AbstractController
{
    /**
     * @Route("/bundesliga/{matchDay}/matchDay", name="bundesliga_table_matchDay", requirements={"matchDay"="\d+"}), , defaults={"matchDays": 1})
     */
    public function actualSeason(TableCatcher $tableCatcher, string $matchDay)
    {

        //todo: actualSason by actualFlag ... redirect if not found
        //todo: partials
        $actualSeason = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find(8);
        $league = $actualSeason->getLeague();
        $season = $actualSeason->getDGoBIndex();
        $lastMatchDay = $this->getDoctrine()->getRepository(BundesligaTable::class)->findLastMatchDay($season, $league);
        $maxMatchDays = $actualSeason->getTeams()->count() - 1;
        $matchDays = range(1, $maxMatchDays);

        if ((int) $matchDay > (int) $lastMatchDay) {
            $matchDay = $lastMatchDay;
        }

        $table = $this->getDoctrine()->getRepository(BundesligaTable::class)->findBy(
            [
                'season' => $season,
                'league' => $league,
                'matchDay' => $matchDay,
            ],
            ['position' => 'ASC']
        );

        //todo: make a cmd
        if (0 === count($table)) {
            /** @var BundesligaTable[] $table */
            $table = $tableCatcher->extract($season, $league, $matchDay);
        }

        //TODO: CMD for data grabbing
        //BundesligaTransfer $transfer
        // $transfer->transfer('2019_2020', '2', true);

        return $this->render('bundesliga/index.html.twig', [
            'table' => $table,
            'title' => $table[0]->getTitle(),
            'matchDays' => $matchDays,
            'actual' => $matchDay,
            'lastMatchDay' => $lastMatchDay,
        ]);
    }
}
