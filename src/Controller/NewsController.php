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

use App\Entity\NewsReader;
use App\Entity\User;
use App\Form\CreateNewsType;
use App\Form\Model\NewsModel;
use App\Form\Model\SubscribeFormModel;
use App\Form\SubscribeType;
use App\Message\ConfirmSubscription;
use App\Services\CoronaNewsDeliverer;
use App\Services\NewsDeliverer;
use App\Tools\NextClubMeeting;
use App\Tools\NextWeeklyMeeting;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NewsController!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 * @Route("/news", name="news_")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/send", name="send")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function send(Request $request, NewsDeliverer $newsDeliverer, NextClubMeeting $nextClubMeeting)
    {
        $strDate = $nextClubMeeting->calcNextMeetingDate();
        $dueDate = \DateTime::createFromFormat(NextClubMeeting::DATE_FORMAT, $strDate);
        $allReaders = $this->getDoctrine()->getRepository(NewsReader::class)->findAll();

        if ('news_send' === $request->attributes->get('_route') && $request->isMethod('POST') && $this->isCsrfTokenValid('send-news', $request->request->get('token'))
        ) {
            $selectedReaders = $request->request->get('readers');
            $readers = [];
            foreach ($selectedReaders as $readerId) {
                $readers[] = $this->getDoctrine()->getRepository(NewsReader::class)->find($readerId);
            }

            $newsDeliverer->deliver($dueDate, $readers);

            $this->addFlash('success', 'Nachrichten verschickt!');

            return $this->redirectToRoute('easyadmin', [
                        'entity' => 'NewsLetter',
                        'action' => 'list',
                        'menuIndex' => '4',
                        'submenuIndex' => '-1',
                        'page' => '1',
            ]);
        }

        return $this->render('news/send_news.html.twig', [
                'dueDate' => $dueDate->format('d.m.Y'),
                'subscribers' => $allReaders,
        ]);
    }

    /**
     * @Route("/corona", name="corona")
     */
    public function corona(CoronaNewsDeliverer $newsDeliverer, NextWeeklyMeeting $nextMeeting)
    {
        $strDate = $nextMeeting->calcNextMeetingDate();
        $dueDate = \DateTime::createFromFormat(NextWeeklyMeeting::DATE_FORMAT, $strDate);
        $allReaders = $this->getDoctrine()->getRepository(NewsReader::class)->findBy(['email' => 'holger@nakade.de']);
        $newsDeliverer->deliver($dueDate, $allReaders);

        $this->addFlash('success', 'Nachrichten verschickt!');

        return $this->render('news/send_news.html.twig', [
                'dueDate' => $dueDate->format('d.m.Y'),
                'subscribers' => $allReaders,
        ]);
    }

    /**
     * @Route("/subscribe", name="subscribe")
     */
    public function subscribe(Request $request, MessageBusInterface $messageBus): Response
    {
        $form = $this->createForm(SubscribeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SubscribeFormModel $subscriber */
            $subscriber = $form->getData();

            $reader = new NewsReader();
            $reader->setEmail($subscriber->email)
                ->setFirstName($subscriber->firstName)
                ->setLastName($subscriber->lastName)
            ;

            //proof if user is registered for setting flag and user is already confirmed
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $subscriber->email]);
            if ($user) {
                $user->setNewsletter(true);
                // set flag in reader
                $reader->setConfirmed($user->isConfirmed());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reader);
            $entityManager->flush();

            //subscription confirmation mail is send only if not confirmed
            if (false === $reader->isConfirmed()) {
                $message = new ConfirmSubscription($reader);
                $messageBus->dispatch($message);
            }

            $this->addFlash('success', 'Du hast den Newsletter abonniert!');

            return $this->render('news/success.html.twig', [
                    'email' => $reader->getEmail(),
            ]);
        }

        return $this->render('news/subscribe.html.twig', [
                'subscribeForm' => $form->createView(),
        ]);
    }

    /**
     * Confirm the subscription email!
     *
     * @Route("/confirm/{token}", name="confirm")
     */
    public function confirm(string $token): Response
    {
        $reader = $this->getDoctrine()->getRepository(NewsReader::class)->findOneBy(['subscribeToken' => $token]);

        if (!$reader) {
            throw new NotFoundHttpException('Data not found!');
        }

        if (false === $reader->isConfirmed()) {
            $reader->setConfirmed(true);
            $this->getDoctrine()->getManager()->flush();
        }
        $this->addFlash('success', 'Deine Email wurde bestätigt!');

        return $this->render('security/confirm.html.twig', ['email' => $reader->getEmail()]);
    }

    /**
     * unsubscribe newsletter!
     *
     * @Route("/unsubscribe/{token}", name="unsubscribe", requirements={"token"=".+"})
     */
    public function unsubscribe(string $token, LoggerInterface $logger): Response
    {
        $reader = $this->getDoctrine()->getRepository(NewsReader::class)->findOneBy(['unsubscribeToken' => $token]);
        if (!$reader) {
            throw new NotFoundHttpException('Data not found!');
        }
        $logger->notice(sprintf('<%s> unsubscribed newsletter', $reader->getEmail()));

        $email = $reader->getEmail();
        $this->getDoctrine()->getManager()->remove($reader);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Der Newsletter wurde storniert!');

        return $this->render('news/unsubscribe.html.twig', ['email' => $email]);
    }

    /**
     * @Route("/create", name="create")
     *
     * @IsGranted("ROLE_NAKADE_TEAM_MANAGER")
     */
    public function create(Request $request): Response
    {
        $model = new NewsModel();
        $form = $this->createForm(CreateNewsType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //todo
        }

        return $this->render('news/create.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}
