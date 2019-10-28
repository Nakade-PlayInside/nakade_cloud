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
use App\Entity\Bundesliga\BundesligaPlayer;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\ContactMail;
use App\Entity\Feature;
use App\Entity\User;
use App\Form\ContactReplyType;
use App\Message\ReplyContact;
use App\Repository\Bundesliga\BundesligaSeasonRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        foreach($playerAsso->getSeasons() as $season) {
            $season->addPlayer($player);
        };
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

    protected function createBundesligaResultsEntityFormBuilder($entity, $view)
    {
        $form = parent::createEntityFormBuilder($entity, $view);
        $form
            ->add('matchDay', TextType::class)
            ->add('playedAt', TextType::class)

#              - { property: 'home', label: 'easyAdmin.bundesliga.results.home' }
#              - { property: 'away', label: 'easyAdmin.bundesliga.results.away' }
#              - { property: 'result', label: 'easyAdmin.bundesliga.results.points',
            ->add('season', EntityType::class , [
            'class' => BundesligaSeason::class,
            'query_builder' => function (BundesligaSeasonRepository $repository) {
                return $repository->createQueryBuilder('s')->andWhere('s.id =1')->orderBy('s.title', 'DESC');
            },
        ] );

        return $form;
    }
}
