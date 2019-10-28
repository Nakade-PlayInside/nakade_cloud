<?php

namespace App\Controller;

use App\Entity\Bundesliga\BundesligaResults;
use App\Form\BundesligaResultsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBundesligaController extends AbstractController
{
    /**
     * @Route("/admin/bundesliga/results/{id}/edit", name="admin_bundesliga_results_edit")
     */
    public function newResultsAction(BundesligaResults $result, Request $request)
    {
        $form = $this->createForm(BundesligaResultsType::class, $result);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();

            if (!assert($result instanceof BundesligaResults)) {
                throw new UnexpectedTypeException($result, BundesligaResults::class);
            }

            $this->getDoctrine()->getManager()->persist($result);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'easyAdmin.bundesliga.results.success');
//
//            return $this->redirectToRoute('easyadmin', [
//                    'entity' => 'Feature',
//                    'action' => $params['action'],
//                    'id' => $feature->getId(),
//                    'menuIndex' => $params['menuIndex'],
//                    'submenuIndex' => $params['submenuIndex'],
//            ]);
        }

        return $this->render('admin_bundesliga/new.html.twig', [
                'form' => $form->createView(),
//                'id' => $feature->getId(),
//                'entity' => $feature,
        ]);
    }
}
