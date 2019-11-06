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
use App\Entity\Bundesliga\BundesligaPenalty;
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaRelegation;
use App\Entity\Bundesliga\BundesligaRelegationMatch;
use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\ContactMail;
use App\Entity\Feature;
use App\Entity\User;
use App\Form\BundesligaMatchType;
use App\Form\BundesligaPenaltyType;
use App\Form\BundesligaRelegationMatchType;
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
class AdminController extends EasyAdminController
{
    protected function updateUserEntity(User $user)
    {
        if ($this->request->request->has('roles')) {
            $roles = $this->request->request->get('roles');
            $user->setRoles($roles);
        }
        $this->getDoctrine()->getManager()->flush();
    }

    protected function updateBundesligaPlayerEntity(BundesligaPlayer $player)
    {
        /** @var BundesligaPlayer $playerA */
        $playerAsso = $this->getDoctrine()->getRepository(BundesligaPlayer::class)->findPlayerByIdWithSeasons($player->getId());

        /** @var BundesligaSeason $season */
        foreach ($playerAsso->getSeasons() as $season) {
            $season->addPlayer($player);
        }
        $this->getDoctrine()->getManager()->flush();
    }

    protected function persistFeatureEntity(Feature $feature)
    {
        $user = $this->getUser();
        if (!assert($user instanceof User)) {
            throw new UnexpectedTypeException($user, User::class);
        }
        $feature->setAuthor($user);

        $this->getDoctrine()->getManager()->persist($feature);
        $this->getDoctrine()->getManager()->flush();
    }

    protected function persistBugReportEntity(BugReport $bugReport)
    {
        $user = $this->getUser();
        if (!assert($user instanceof User)) {
            throw new UnexpectedTypeException($user, User::class);
        }
        $bugReport->setAuthor($user);

        $this->getDoctrine()->getManager()->persist($bugReport);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @Route("/contact/reply", name="app_contact_reply")
     */
    public function replyContact(Request $request, MessageBusInterface $messageBus)
    {
        $contactId = $request->get('id');
        $params = $request->get('parameters');
        $contact = $this->getDoctrine()->getRepository(ContactMail::class)->find($contactId);
        $href = sprintf(
            '/admin/?entity=%s&action=%s&menuIndex=%s&submenuIndex=%s&sortField=%s&sortDirection=%s&page=%s',
            $params['entity'],
            $params['action'],
            $params['menuIndex'],
            $params['submenuIndex'],
            $params['sortField'],
            $params['sortDirection'],
            $params['page'],
        );

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

            return $this->redirectToRoute('easyadmin', [
                    'entity' => $params['entity'],
                    'menuIndex' => $params['menuIndex'],
                    'action' => $params['action'],
                    'submenuIndex' => $params['submenuIndex'],
                    'page' => $params['page'],
                    'sortField' => $params['sortField'],
                    'sortDirection' => $params['sortDirection'],
            ]);
        }

        return $this->render('admin/contact/reply.html.twig', [
                'form' => $form->createView(),
                'contact' => $contact,
                'href' => $href,
        ]);
    }

    /**
     * @Route("/contact/remove", name="app_contact_remove")
     */
    public function removeContact(Request $request)
    {
        $contactId = $request->get('id');
        $contact = $this->getDoctrine()->getRepository(ContactMail::class)->find($contactId);
        $this->getDoctrine()->getManager()->remove($contact);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'easyAdmin.flash.contact.removed');

        return $this->redirectToRoute('easyadmin', [
                'action' => 'list',
                'entity' => 'ContactMail',
                'menuIndex' => 1,
        ]);
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

    protected function editBundesligaResultsAction()
    {
        $id = $this->request->get('id');
        $params = $this->request->query->all();

        $results = $this->em->getRepository(BundesligaResults::class)->find($id);
        $form = $this->createForm(BundesligaResultsType::class, $results);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();

            if (!assert($result instanceof BundesligaResults)) {
                throw new UnexpectedTypeException($result, BundesligaResults::class);
            }

            $this->getDoctrine()->getManager()->persist($result);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.results.update.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }

        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/results/form.html.twig', $params);
    }

    protected function newBundesligaResultsAction()
    {
        $params = $this->request->query->all();

        $form = $this->createForm(BundesligaResultsType::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();

            if (!assert($result instanceof BundesligaResults)) {
                throw new UnexpectedTypeException($result, BundesligaResults::class);
            }

            $this->getDoctrine()->getManager()->persist($result);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.results.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }
        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/results/form.html.twig', $params);
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

    protected function editBundesligaMatchAction()
    {
        $id = $this->request->get('id');
        $params = $this->request->query->all();

        $match = $this->em->getRepository(BundesligaMatch::class)->find($id);
        $form = $this->createForm(BundesligaMatchType::class, $match);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $match = $form->getData();

            if (!assert($match instanceof BundesligaMatch)) {
                throw new UnexpectedTypeException($match, BundesligaMatch::class);
            }

            $this->getDoctrine()->getManager()->persist($match);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.match.update.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }

        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/match/form.html.twig', $params);
    }

    protected function newBundesligaMatchAction()
    {
        $params = $this->request->query->all();

        $form = $this->createForm(BundesligaMatchType::class);
        $form->handleRequest($this->request);

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

        return $this->render('admin/bundesliga/match/form.html.twig', $params);
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

    protected function editBundesligaRelegationMatchAction()
    {
        $id = $this->request->get('id');
        $params = $this->request->query->all();

        $match = $this->em->getRepository(BundesligaRelegationMatch::class)->find($id);
        $form = $this->createForm(BundesligaRelegationMatchType::class, $match);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $match = $form->getData();

            if (!assert($match instanceof BundesligaRelegationMatch)) {
                throw new UnexpectedTypeException($match, BundesligaRelegationMatch::class);
            }

            $this->getDoctrine()->getManager()->persist($match);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.match.update.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }

        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/relegation_match/form.html.twig', $params);
    }

    protected function newBundesligaRelegationMatchAction()
    {
        $params = $this->request->query->all();

        $form = $this->createForm(BundesligaRelegationMatchType::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $match = $form->getData();

            if (!assert($match instanceof BundesligaRelegationMatch)) {
                throw new UnexpectedTypeException($match, BundesligaRelegationMatch::class);
            }

            $this->getDoctrine()->getManager()->persist($match);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.match.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }
        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/relegation_match/form.html.twig', $params);
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

    protected function editBundesligaPenaltyAction()
    {
        $id = $this->request->get('id');
        $params = $this->request->query->all();

        $penalty = $this->em->getRepository(BundesligaPenalty::class)->find($id);
        $form = $this->createForm(BundesligaPenaltyType::class, $penalty);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $penalty = $form->getData();

            if (!assert($penalty instanceof BundesligaPenalty)) {
                throw new UnexpectedTypeException($penalty, BundesligaPenalty::class);
            }

            $this->getDoctrine()->getManager()->persist($penalty);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.penalty.update.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }

        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/penalty/form.html.twig', $params);
    }

    protected function newBundesligaPenaltyAction()
    {
        $params = $this->request->query->all();

        $form = $this->createForm(BundesligaPenaltyType::class);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $penalty = $form->getData();

            if (!assert($penalty instanceof BundesligaPenalty)) {
                throw new UnexpectedTypeException($penalty, BundesligaPenalty::class);
            }

            $this->getDoctrine()->getManager()->persist($penalty);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.penalty.success');
            $params['action'] = 'list';

            return $this->redirectToRoute('easyadmin', $params);
        }
        $params['form'] = $form->createView();

        return $this->render('admin/bundesliga/penalty/form.html.twig', $params);
    }

    /**
     * @Route("/admin/bundesliga/penalty/season-select", name="admin_bundesliga_penalty_season_select")
     */
    public function getPenaltySeasonSelect(Request $request)
    {
        $seasonId = $request->query->get('seasonId');
        $season = $this->getDoctrine()->getRepository(BundesligaSeason::class)->find($seasonId);

        $penalty = new BundesligaPenalty();
        $penalty->setSeason($season);
        $form = $this->createForm(BundesligaPenaltyType::class, $penalty);

        // no field? Return an empty response
        if (!$form->has('team')) {
            return new Response(null, 204);
        }

        return $this->render('admin/bundesliga/penalty/_team_penalty.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}
