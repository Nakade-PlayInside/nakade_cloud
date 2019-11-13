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

}
