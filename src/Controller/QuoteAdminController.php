<?php

namespace App\Controller;

use App\Repository\Common\QuotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
}
