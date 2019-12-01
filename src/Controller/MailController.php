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

use App\Entity\Bundesliga\BundesligaTeam;
use App\Entity\Bundesliga\ResultMail;
use App\Message\MatchResult;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /**
     * @Route("/mail/{resultMail}/result", name="mail_result", requirements={"resultMail"="\d+"})
     *
     * @IsGranted("ROLE_NAKADE_TEAM_MANAGER")
     */
    public function sendResultMail(Request $request, ResultMail $resultMail, MessageBusInterface $messageBus)
    {
        $resultMail = $this->getDoctrine()->getRepository(ResultMail::class)->find($resultMail);
        if (!$resultMail) {
            return $this->redirect('bundesliga_actual_matchDay');
        }
        //todo: no captain
        //todo: no email
        //todo: no executive

        $team = $this->getDoctrine()->getRepository(BundesligaTeam::class)->findTeamNakade();
        $names = explode(' ', $team->getCaptain());
        $manager = $names[0];
        $managerEmail = $team->getEmail();

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultMail->setSendAt(new \DateTimeImmutable());

            //mail handling
            $message = new MatchResult($resultMail, $manager, $managerEmail);
            $messageBus->dispatch($message);

            $this->addFlash('success', 'result.mail.success');
        }

        return $this->render(
            'mail/result.html.twig',
            [
                'mail' => $resultMail,
                'email' => $resultMail->getResults()->getSeason()->getExecutive()->getEmail(),
                'manager' => $manager,
                'form' => $form->createView(),
            ]
        );
    }
}
