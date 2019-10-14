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

use App\Entity\ContactMail;
use App\Entity\Quotes;
use App\Form\ContactReplyType;
use App\Form\ContactType;
use App\Message\ConfirmContact;
use App\Tools\NextClubMeeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Default Controller!
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $quotes = $entityManager->getRepository(Quotes::class)->findAll();
        shuffle($quotes);

        return $this->render('default/index.html.twig', ['quotes' => $quotes]);
    }

    /**
     * @Route("/about", name="app_about")
     */
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }

    /**
     * @Route("/clubs", name="app_clubs")
     */
    public function clubs(): Response
    {
        $meetingDate = (new NextClubMeeting())->calcNextMeetingDate();
        $googleMapsLink = 'https://www.google.de/maps/place/Mommsen-Eck+Charlottenburg+-+Haus+der+100+Biere/@52.5033712,13.3061993,17z/data=!4m5!3m4!1s0x47a850e713b8bc8d:0xc3bbcc7aaab481fb!8m2!3d52.5033692!4d13.3083839';

        return $this->render('default/clubs.html.twig', [
                'googleMapsLink' => $googleMapsLink,
                'meetingDate' => $meetingDate,
        ]);
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(Request $request, MessageBusInterface $messageBus)
    {
        // creates a task object and initializes some data for this example
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$contact` variable has also been updated
            $contact = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            //mail handling
            $message = new ConfirmContact($contact);
            $messageBus->dispatch($message);

            $this->addFlash('success', 'Nachricht verschickt!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('default/contact.html.twig', [
                'contactForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/imprint", name="app_imprint")
     */
    public function imprint(): Response
    {
        return $this->render('default/imprint.html.twig');
    }

    /**
     * @Route("/privacy", name="app_privacy")
     */
    public function privacy()
    {
        return $this->render('default/privacy.html.twig');
    }

    /**
     * @Route("/contact/reply", name="app_contact_reply")
     */
    public function replyContact(Request $request)
    {
        $contactId = $request->get('id');
        $contact = $this->getDoctrine()->getRepository(ContactMail::class)->find($contactId);

        $form = $this->createForm(ContactReplyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactReply = $form->getData();
            $contactReply->setEditor($this->getUser())
                    ->setRecipient($contact);

            $this->getDoctrine()->getManager()->persist($contactReply);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('admin/contact/reply.html.twig', [
              'form' => $form->createView(),
              'contact' => $contact,
        ]);
    }
}
