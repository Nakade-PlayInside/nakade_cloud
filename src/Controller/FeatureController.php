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

use App\Entity\FeatureComment;
use App\Entity\Feature;
use App\Entity\User;
use App\Form\CommentType;
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
class FeatureController extends AbstractController
{
    /**
     * @Route("/feature/comment/{id}", name="app_feature_comment")
     *
     * @ParamConverter("feature", class="App\Entity\Feature")
     */
    public function comment(Request $request, Feature $feature)
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            if (!assert($comment instanceof FeatureComment)) {
                throw new UnexpectedTypeException($comment, FeatureComment::class);
            }
            $user = $this->getUser();
            if (!assert($user instanceof User)) {
                throw new UnexpectedTypeException($user, User::class);
            }

            $comment->setAuthor($user);
            $this->getDoctrine()->getManager()->persist($comment);

            $feature->addComment($comment);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Kommentar hinzugefügt!');

            return $this->redirectToRoute('easyadmin', [
                 'entity' => 'Feature',
                 'action' => 'show',
                 'id' => $feature->getId(),
                 'menuIndex' => 6,
            ]);
        }

        return $this->render('feature/comment.html.twig', [
                'form' => $form->createView(),
                'id' => $feature->getId(),
                'entity' => $feature,
        ]);
    }
}
