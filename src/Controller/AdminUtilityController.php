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

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaPenalty;
use App\Entity\Bundesliga\BundesligaRelegation;
use App\Entity\Bundesliga\BundesligaRelegationMatch;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\ContactMail;
use App\Entity\User;
use App\Form\BundesligaMatchType;
use App\Form\BundesligaPenaltyType;
use App\Form\BundesligaRelegationMatchType;
use App\Form\BundesligaRelegationType;
use App\Form\BundesligaResultsType;
use App\Form\ContactReplyType;
use App\Message\ReplyContact;
use App\Repository\Bundesliga\BundesligaOpponentRepository;
use App\Repository\Bundesliga\BundesligaTeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminUtilityController extends AbstractController
{
    /**
     * @Route("/admin/utility/teams", methods="GET", name="admin_utility_teams")
     */
    public function getTeamsApi(BundesligaTeamRepository $repository, Request $request)
    {
        $teams = $repository->findAllMatching($request->query->get('query'));

        return $this->json([
                'teams' => $teams,
        ], 200, [], ['groups' => ['main']]);
    }

    /**
     * @Route("/admin/utility/opponent", methods="GET", name="admin_utility_opponent")
     */
    public function getOpponentApi(BundesligaOpponentRepository $repository, Request $request)
    {
        $opponent = $repository->findAllMatching($request->query->get('query'));

        return $this->json([
                'opponent' => $opponent,
        ], 200, [], ['groups' => ['main']]);
    }

    /**
     * @Route("/phpinfo", name="easyadmin_phpinfo")
     */
    public function phpInfoAction(): Response
    {
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }
        ob_start();
        phpinfo();
        $str = ob_get_contents();
        ob_get_clean();

        return new Response($str);
    }


    /**
     * @Route("/contact/reply", name="app_contact_reply")
     */
    public function replyContact(Request $request, MessageBusInterface $messageBus)
    {
        $contactId = $request->get('id');
        $params = $request->query->all();

        $contact = $this->getDoctrine()->getRepository(ContactMail::class)->find($contactId);
        $params['contact'] = $contact;

        $form = $this->createForm(ContactReplyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactReply = $form->getData();
            $contactReply->setEditor($this->getUser())
                    ->setRecipient($contact);

            $this->getDoctrine()->getManager()->persist($contactReply);
            $this->getDoctrine()->getManager()->flush();

            //mail handling
            $message = new ReplyContact($contactReply);
            $messageBus->dispatch($message);

            $this->addFlash('success', 'easyAdmin.flash.message.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }
        $params['form'] = $form->createView();

        return $this->render('admin/contact/reply.html.twig', $params);
    }

    /**
     * @Route("/contact/remove", name="app_contact_remove")
     */
    public function removeContact(Request $request)
    {
        $contactId = $request->get('id');
        $params = $request->query->all();

        $contact = $this->getDoctrine()->getRepository(ContactMail::class)->find($contactId);
        $this->getDoctrine()->getManager()->remove($contact);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'easyAdmin.flash.contact.removed');
        $params['action'] = 'list';
        unset($params['id']);

        return $this->redirectToRoute('easyadmin', $params);
    }

    /**
     * @Route("/profile/impersonate", name="app_profile_impersonate")
     *
     * @IsGranted({"ROLE_SUPER_ADMIN", "IS_AUTHENTICATED_FULLY"})
     */
    public function impersonate(Request $request): Response
    {
        $userId = $request->get('id');
        if (!$userId) {
            $this->addFlash('info', 'Switch User hat nicht funktioniert!');
            $referer = $request->headers->get('referer');

            return $this->redirect($referer);
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        $this->addFlash('success', 'Switch User erfolgreich!');

        return $this->redirectToRoute('app_homepage', ['_switch_user' => $user->getEmail()]);
    }


    /**
     * @Route("/admin/bundesliga/results/season-select", name="admin_bundesliga_results_season_select")
     */
    public function getTeamsSeasonSelect(Request $request)
    {
        $seasonId = $request->query->get('seasonId');
        $season = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find($seasonId);

        $results = new BundesligaResults();
        $results->setSeason($season);
        $form = $this->createForm(BundesligaResultsType::class, $results);

        // no field? Return an empty response
        if (!$form->has('home') || !$form->has('away')) {
            return new Response(null, 204);
        }

        return $this->render('admin/bundesliga/results/_teams_result.html.twig', [
                'teamForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/bundesliga/match/season-select", name="admin_bundesliga_match_season_select")
     */
    public function getMatchSeasonSelect(Request $request)
    {
        $seasonId = $request->query->get('seasonId');
        $season = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find($seasonId);

        $match = new BundesligaMatch();
        $match->setSeason($season);
        $form = $this->createForm(BundesligaMatchType::class, $match);

        // no field? Return an empty response
        if (!$form->has('results')) {
            return new Response(null, 204);
        }

        return $this->render('admin/bundesliga/match/_result_match.html.twig', [
                'resultForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/bundesliga/relegation/season-select", name="admin_bundesliga_relegation_season_select")
     */
    public function getRelegationSeasonSelect(Request $request)
    {
        $seasonId = $request->query->get('seasonId');
        $season = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find($seasonId);

        $relegation = new BundesligaRelegation();
        $relegation->setSeason($season);
        $form = $this->createForm(BundesligaRelegationType::class, $relegation);

        // no field? Return an empty response
        if (!$form->has('home') || !$form->has('away')) {
            return new Response(null, 204);
        }

        return $this->render('admin/bundesliga/results/_teams_result.html.twig', [
                'teamForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/bundesliga/relegation/match/season-select", name="admin_bundesliga_relegation_match_season_select")
     */
    public function getRelegationMatchSeasonSelect(Request $request)
    {
        $seasonId = $request->query->get('seasonId');
        $season = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find($seasonId);

        $match = new BundesligaRelegationMatch();
        $match->setSeason($season);
        $form = $this->createForm(BundesligaRelegationMatchType::class, $match);

        // no field? Return an empty response
        if (!$form->has('results')) {
            return new Response(null, 204);
        }

        return $this->render('admin/bundesliga/relegation_match/_result_match.html.twig', [
                'resultForm' => $form->createView(),
        ]);
    }
}
