<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ChangeLogController extends AbstractController
{
    /**
     * @Route("/change/log", name="change_log")
     */
    public function index()
    {
        return $this->render('change_log/index.html.twig', [
            'controller_name' => 'ChangeLogController',
        ]);
    }
}
