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

use App\Controller\Helper\NextClubMeeting;
use App\Entity\NewsReader;
use App\Entity\User;
use App\Form\Model\SubscribeFormModel;
use App\Form\SubscribeType;
use App\Message\ConfirmRegistration;
use App\Message\News;
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
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/news", name="news")
     */
    public function index()
    {
        return $this->render('news/index.html.twig', [
            'controller_name' => 'NewsController',
        ]);
    }

    /**
     * @Route("/news/send", name="news_send")
     */
    public function send(MessageBusInterface $messageBus, NextClubMeeting $nextClubMeeting)
    {
        //  todo: make service
        $date = $nextClubMeeting->calcNextMeetingDate();

        //mail handling
        $message = new News('holger@nakade.de', $date, '1234ewrer');
        $messageBus->dispatch($message);

        $this->addFlash('success', 'Nachricht verschickt!');

        return $this->render('news/index.html.twig', [
                'controller_name' => 'NewsController',
        ]);
    }

    /**
     * @Route("/news/subscribe", name="news_subscribe")
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
                ->setSubscribeToken($subscriber->subscribeToken)
                ->setUnsubscribeToken($subscriber->unSubscribeToken)
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
                $message = new ConfirmRegistration($reader);
                $messageBus->dispatch($message);
            }

            $this->addFlash('success', 'Du hast den Newsletter abonniert!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('news/subscribe.html.twig', [
                'subscribeForm' => $form->createView(),
        ]);
    }

    /**
     * Confirm the registered email!
     *
     * @Route("/news/confirm/{token}", name="news_confirm")
     */
    public function confirm(string $token): Response
    {
//        todo
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['confirmToken' => $token]);

        if (!$user) {
            throw new NotFoundHttpException('Data not found!');
        }

        if (false === $user->isConfirmed()) {
            $user->setConfirmed(true);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Deine email wurde bestätigt!');
        }

        return $this->render('security/confirm.html.twig');
    }
}
