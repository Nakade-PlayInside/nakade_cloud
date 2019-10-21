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
use App\Entity\BugComment;
use App\Entity\User;
use App\Form\BugCommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 * @IsGranted("ROLE_ADMIN")
 */
class BugController extends AbstractController
{
    /**
     * @Route("/bug/comment/{id}", name="app_bug_comment")
     *
     * @ParamConverter("bugReport", class="App\Entity\BugReport")
     */
    public function comment(Request $request, BugReport $bugReport)
    {
        $form = $this->createForm(BugCommentType::class);
        $form->handleRequest($request);
        $params = $request->query->get('parameters');

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            if (!assert($comment instanceof BugComment)) {
                throw new UnexpectedTypeException($comment, BugComment::class);
            }
            $user = $this->getUser();
            if (!assert($user instanceof User)) {
                throw new UnexpectedTypeException($user, User::class);
            }

            $comment->setAuthor($user);
            $this->getDoctrine()->getManager()->persist($comment);

            $bugReport->addComment($comment);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Kommentar hinzugefügt!');

            return $this->redirectToRoute('easyadmin', [
                 'entity' => 'BugReport',
                 'action' => $params['action'],
                 'id' => $bugReport->getId(),
                 'menuIndex' => $params['menuIndex'],
                 'submenuIndex' => $params['submenuIndex'],
            ]);
        }

        return $this->render('bug/comment.html.twig', [
                'form' => $form->createView(),
                'id' => $bugReport->getId(),
                'entity' => $bugReport,
        ]);
    }
}
