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

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Form\CaptainResultInputType;
use App\Form\Model\ResultModel;
use App\Services\ActualResultsGrabber;
use App\Services\ActualTableService;
use App\Services\BundesligaTableService;
use App\Services\Model\TableModel;
use App\Tools\Bundesliga\Model\PlayerStats;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BundesligaController extends AbstractController
{
    /**
     * @Route("/bundesliga/test", name="bundesliga_test")
     */
    public function actualSeason(ActualResultsGrabber $service)
    {
        /** @var TableModel $model */
        $model = $service->retrieveTable();

        return $this->render('bundesliga/index.html.twig', [
            'model' => $model,
        ]);
    }

    /**
     * @Route("/bundesliga/actualMatchDay", name="bundesliga_actual_matchDay")
     */
    public function actualMatch(ActualTableService $tableService, Request $request)
    {
        $actualSeason = $this->getDoctrine()->getRepository(BundesligaSeason::class)->findOneBy(['actualSeason' => true]);
        if (!$actualSeason) {
            return $actualSeason;
        }

        $matchDay = $this->getDoctrine()->getRepository(BundesligaResults::class)->findActualMatchDay($actualSeason);
        $result = $this->getDoctrine()
                ->getRepository(BundesligaResults::class)
                ->findNakadeResult($actualSeason->getId(), $matchDay);

        $model = new ResultModel();
        $model->season = $actualSeason;
        $model->results = $result;

        $form = $this->createForm(CaptainResultInputType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $match = $form->getData();

            if (!assert($match instanceof BundesligaMatch)) {
                throw new UnexpectedTypeException($match, BundesligaMatch::class);
            }

            $this->getDoctrine()->getManager()->persist($match);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.match.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }
        $params['form'] = $form->createView();

        return $this->render(
            'bundesliga/form.matchday.html.twig',
            [
                'form' => $form->createView(),
                'model' => $model,
            ]
        );
    }

    /**
     * @Route("/bundesliga/season/archive", name="bundesliga_season_archive")
     *
     * @IsGranted("ROLE_USER")
     */
    public function showSeasonArchive(BundesligaTableService $tableService, Request $request)
    {
        $seasonId = $matchDay = null;
        if ($request->query->has('seasonId')) {
            $seasonId = $request->query->get('seasonId');
        }
        if ($request->query->has('matchDay')) {
            $matchDay = $request->query->get('matchDay');
        }

        /** @var TableModel $model */
        $model = $tableService->retrieveTable($seasonId, $matchDay);
        $matches = null;
        $allSeasons = $this->getDoctrine()->getRepository(BundesligaSeason::class)->findAll();
        if ($model) {
            $matches = $model->getResult()->getMatches();
        }


        return $this->render('bundesliga/season.matchDay.html.twig', [
                'allSeasons' => $allSeasons,
                'matches' => $matches,
                'model' => $model,
        ]);
    }

    /**
     * @Route("/bundesliga/season/player", name="bundesliga_season_player")
     *
     * @IsGranted("ROLE_USER")
     */
    public function showSeasonPlayer(BundesligaTableService $tableService, Request $request)
    {
        $seasonId = $playerId = null;
        if ($request->query->has('seasonId')) {
            $seasonId = $request->query->get('seasonId');
        }
        if ($request->query->has('playerId')) {
            $playerId = $request->query->get('playerId');
        }

        /** @var BundesligaMatch[] $matches */
        $matches = $this->getDoctrine()->getRepository(BundesligaMatch::class)->findPlayerMatches($seasonId, $playerId);

        $model = new PlayerStats($matches[0]->getPlayer());
        foreach ($matches as $match) {
            'w' === $match->getColor() ? $model->white++ : $model->black++;

            switch ($match->getResult()) {
                case '2:0':
                    $model->wins++;
                    ++$model->games;
                    break;
                case '1:1':
                    $model->draws++;
                    ++$model->games;
                    break;
                case '0:2':
                    $model->losses++;
                    ++$model->games;
                    break;
            }

            switch ($match->getBoard()) {
                case 1:
                    $model->firstBoard++;
                    break;
                case 2:
                    $model->secondBoard++;
                    break;
                case 3:
                    $model->thirdBoard++;
                    break;
                case 4:
                    $model->fourthBoard++;
                    break;
            }
        }

        return $this->render('bundesliga/season.player.html.twig', [
                'model' => $model,
        ]);
    }
}
