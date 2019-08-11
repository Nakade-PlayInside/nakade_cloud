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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Class Common Controller!
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class CommonController extends AbstractController
{
    /**
     * The about page!
     *
     * @return Response
     *
     * @throws Exception
     *
     * @Route("/about", name="common_about")
     */
    public function about()
    {
        return $this->render('common/about.html.twig');
    }

    /**
     * The imprint page!
     *
     * @return Response
     *
     * @throws Exception
     *
     * @Route("/imprint", name="common_imprint")
     */
    public function imprint()
    {
        return $this->render('common/imprint.html.twig');
    }

    /**
     * The privacy statement!
     *
     * @return Response
     *
     * @throws Exception
     *
     * @Route("/privacy", name="common_privacy")
     */
    public function privacy()
    {
        return $this->render('common/privacy.html.twig');
    }

    /**
     * The contact page!
     *
     * @return Response
     *
     * @throws Exception
     *
     * @Route("/contact", name="common_contact")
     */
    public function contact()
    {
        return $this->render('common/contact.html.twig');
    }
}