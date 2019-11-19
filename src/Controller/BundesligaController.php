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
use App\Form\BundesligaMatchType;
use App\Form\CaptainResultInputType;
use App\Form\MatchDayResultType;
use App\Form\Model\ResultModel;
use App\Services\ActualTableGrabber;
use App\Services\ActualTableService;
use App\Services\Model\TableModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BundesligaController extends AbstractController
{
    /**
     * @Route("/bundesliga/{matchDay}/matchDay", name="bundesliga_table_matchDay", requirements={"matchDay"="\d+"}), defaults={"matchDays": 1})
     */
    public function actualSeason(ActualTableService $tableService, string $matchDay)
    {
        /** @var TableModel $model */
        $model = $tableService->retrieveTable($matchDay);

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
        $matchDay=1;
        $result = $this->getDoctrine()
                ->getRepository(BundesligaResults::class)
                ->findNakadeResult($actualSeason->getId(), $matchDay);

        $model= new ResultModel();
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

        return $this->render('bundesliga/form.matchday.html.twig',
            [
                'form' => $form->createView(),
                'model' =>  $model,

            ]
        );
    }

}
