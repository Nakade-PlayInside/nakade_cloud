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

use App\Entity\Common\Quotes;
use App\Form\Type\Admin\QuotesType;
use App\Repository\Common\QuotesRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QuoteAdminController!
 *
 * @package App\Controller
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class QuoteAdminController extends AbstractController
{
    /**
     * @Route("/admin/quote", name="quote_admin")
     *
     * @param QuotesRepository $repository
     * @param Request          $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(QuotesRepository $repository, Request $request): Response
    {
        $quotes = $repository->findAll();

        return $this->render('quote_admin/index.html.twig', [
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
     * @Route("/admin/quote/add", name="add_quote_admin")
     */
    public function quotes(Request $request): Response
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

        return $this->render('quote_admin/add_quote.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}
