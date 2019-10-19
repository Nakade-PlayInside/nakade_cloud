<?php

namespace App\Controller;

use App\Entity\Feature;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FeatureController extends AbstractController
{
    /**
     * @Route("/feature", name="app_feature")
     */
    public function index()
    {
        $allFeatures = $this->getDoctrine()->getRepository(Feature::class)->findBy(['closedAt' => null]);

        return $this->render('feature/index.html.twig', [
            'allFeatures' => $allFeatures,
        ]);
    }
}
