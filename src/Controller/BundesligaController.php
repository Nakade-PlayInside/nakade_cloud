<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BundesligaController extends AbstractController
{
    /**
     * @Route("/bundesliga", name="bundesliga")
     */
    public function index()
    {
        return $this->render('bundesliga/index.html.twig', [
            'controller_name' => 'BundesligaController',
        ]);
    }
}
