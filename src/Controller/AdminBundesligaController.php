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

namespace App\Controller;

use App\Entity\BugReport;
use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaRelegation;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\ContactMail;
use App\Entity\Feature;
use App\Entity\User;
use App\Form\BundesligaMatchType;
use App\Form\BundesligaRelegationType;
use App\Form\BundesligaResultsType;
use App\Form\ContactReplyType;
use App\Message\ReplyContact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminBundesligaController extends EasyAdminController
{
    protected function editBundesligaRelegationAction()
    {
        $id = $this->request->get('id');
        $params = $this->request->query->all();

        $relegation = $this->em->getRepository(BundesligaRelegation::class)->find($id);
        $form = $this->createForm(BundesligaRelegationType::class, $relegation);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relegation = $form->getData();

            if (!assert($relegation instanceof BundesligaRelegation)) {
                throw new UnexpectedTypeException($relegation, BundesligaRelegation::class);
            }

            $this->getDoctrine()->getManager()->persist($relegation);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.relegation.update.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }

        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/relegation/form.html.twig', $params);
    }

    protected function newBundesligaRelegationAction()
    {
        $params = $this->request->query->all();

        $form = $this->createForm(BundesligaRelegationType::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relegation = $form->getData();

            if (!assert($relegation instanceof BundesligaRelegation)) {
                throw new UnexpectedTypeException($relegation, BundesligaRelegation::class);
            }

            $this->getDoctrine()->getManager()->persist($relegation);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.relegation.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }
        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/relegation/form.html.twig', $params);
    }

    /**
     * @Route("/admin/bundesliga/relegation/season-select", name="admin_bundesliga_relegation_season_select")
     */
    public function getTeamsSeasonSelect(Request $request)
    {
        $seasonId = $request->query->get('seasonId');
        $season = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find($seasonId);

        $relegation = new BundesligaRelegation();
        $relegation->setSeason($season);
        $form = $this->createForm(BundesligaResultsType::class, $relegation);

        // no field? Return an empty response
        if (!$form->has('home') || !$form->has('away')) {
            return new Response(null, 204);
        }

        return $this->render('admin/bundesliga/results/_teams_result.html.twig', [
                'teamForm' => $form->createView(),
        ]);
    }

}
