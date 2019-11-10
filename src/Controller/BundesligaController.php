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

use App\Services\Snoopy;
use App\Tools\DGoB\MatchDayCatcher;
use App\Tools\DGoB\SeasonCatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BundesligaController extends AbstractController
{
    /**
     * @Route("/bundesliga", name="bundesliga")
     */
    public function index()
    {
        $str = '';
        //$str = $grabber->grab('http://www.dgob.de/lmo/index.php');
        //dd($str);

        $seasonCatcher = new SeasonCatcher('2');
        $results = $seasonCatcher->extract();
//
//        $snoopy = new Snoopy();
//        //$snoopy->fetchlinks('http://www.dgob.de/lmo/lmo.php?action=table&amp;file=1920_bl2.l98');
//        //$snoopy->fetchtext('http://www.dgob.de/lmo/output/1920_bl2.l98-sp.html');
//        //$snoopy->fetchtext('http://www.dgob.de/lmo/lmo.php?action=results&tabtype=0&file=1920_bl2.l98&st=1#');
//        $snoopy->fetch('http://www.dgob.de/lmo/lmo.php?action=results&tabtype=0&file=1920_bl2.l98&st=1#');
//
//        $action = 'results';
//        $season = '1920'; //season years
//        $league = 'bl2';
//        $matchDay = '9';
//        $linkParams = sprintf("action=%s&tabtype=0&file=%s_%s.l98&st=%s", $action, $season, $league, $matchDay);
//
//        $snoopy->fetch('http://www.dgob.de/lmo/lmo.php?' . $linkParams);
//        $html = $snoopy->results;
//
//        $results = (new MatchDayCatcher($html))->extract($matchDay);

        dd($results);
        return $this->render('bundesliga/index.html.twig', [
            'controller_name' => 'BundesligaController',
        ]);
    }
}
