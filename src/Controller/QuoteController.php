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

use App\Entity\Common\Quotes;
use App\Form\Type\Admin\QuotesType;
use App\Repository\Common\QuotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QuoteController!
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class QuoteController extends AbstractController
{
    /**
     * @Route("/quote", name="quote_index")
     *
     * @param QuotesRepository $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(QuotesRepository $repository): Response
    {
        $quotes = $repository->findAll();

        return $this->render('quote/index.html.twig', [
            'quotes' => $quotes,
        ]);
    }

    /**
     * The quotes page!
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     *
     * @Route("/quote/new", name="quote_new")
     */
    public function new(Request $request): Response
    {
        // creates a task object and initializes some data for this example
        $quotes = new Quotes();
        $form = $this->createForm(QuotesType::class, $quotes);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$contact` variable has also been updated
            $quotes = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quotes);
            $entityManager->flush();
        }

        return $this->render('quote/new.html.twig', [
                'form' => $form->createView(),
        ]);
    }

    /**
     * @param Quotes                 $quote
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/quote/{id}/edit", name="quote_edit")
     */
    public function edit(Quotes $quote, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(QuotesType::class, $quote);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Quotes $quote */
            $quote = $form->getData();
            $em->persist($quote);
            $em->flush();
            $this->addFlash('success', 'Zitat erfolgreich bearbeitet!');

            return $this->redirectToRoute('quote_edit', [
                    'id' => $quote->getId(),
            ]);
        }

        return $this->render('quote/edit.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}
