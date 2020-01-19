<?php

namespace App\Controller;

use App\Entity\Bundesliga\BundesligaSgf;
use App\Entity\SgfFile;
use App\Form\SgfType;
use App\Tools\SgfGameInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSgfController extends AbstractController
{
    /**
     * @Route("/admin/upload/sgf", name="upload_sgf")
     */
    public function temporaryUploadAction(Request $request, SgfGameInfo $gameInfo)
    {
        $sgf = new SgfFile();
        $form = $this->createForm(SgfType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['file']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';

            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );

                $file = $destination.'/'.$newFilename;
                $localPath = 'uploads/'.$newFilename;
                $sgf->setLocalPath($localPath);

                $gameInfo->read($file, $sgf);
                dd($sgf);


                $sgf->setHash(md5_file($file));
dd($sgf);
                $this->getDoctrine()->getManager()->persist($sgf);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Article Updated! Inaccuracies squashed!');
            }
        }

        return $this->render('admin_sgf/edit.html.twig', [
                'sgfForm' => $form->createView(),
                'sgf' => $sgf,
        ]);
    }
}
