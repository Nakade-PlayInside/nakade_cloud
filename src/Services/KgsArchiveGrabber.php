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

namespace App\Services;

use App\Entity\Bundesliga\BundesligaSgf;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints\Timezone;

class KgsArchiveGrabber
{
    //https://www.gokgs.com/gameArchives.jsp?user=nakade01&year=2012&month=9
    //erste tabelle class grid
    //erste spalte = http://files.gokgs.com/games/2012/9/13/AGruKi1-Nakade01.sgf
    //spalte Typ != Besprechung   type==frei || gewertet?

    private $archiveDownload;
    private $manager;
    /**
     * @var ContentRetriever
     */
    private $contentRetriever;

    public function __construct(EntityManagerInterface $manager, KgsArchivesDownload $download, ContentRetriever $contentRetriever)
    {
        $this->archiveDownload = $download;
        $this->manager = $manager;
        $this->contentRetriever = $contentRetriever;
    }

    public function download()
    {
        $uri = 'http://files.gokgs.com/games/2012/9/13/AGruKi1-Nakade01.sgf';
        $season = '2012_13';

        $html = $this->contentRetriever->grab('https://www.gokgs.com/gameArchives.jsp?user=nakade01&year=2012&month=9');
        $crawler = new Crawler($html);
        //second lmoInner table!
        $domNode = $crawler->filter('table.grid')->getNode(0);
        // seven cells expected
        $domNode->firstChild->childNodes->length;

        /** @var \DOMNode $row */
        foreach ($domNode->childNodes as $row) {
            if ('th' === $row->firstChild->nodeName) {
                continue;
            }
            $rowCrawler = new Crawler($row);
            $link = $rowCrawler->filter('td')->getNode(0);
            $playedAtGMT = $rowCrawler->filter('td')->getNode(4)->textContent;
            $gmt = new \DateTimeZone('Europe/London');
            $playedAt = new \DateTime($playedAtGMT, $gmt);
            $cet = new \DateTimeZone('Europe/Berlin');
            $playedAt->setTimezone($cet);
            $type = $rowCrawler->filter('td')->getNode(5)->textContent;
            $result = $rowCrawler->filter('td')->getNode(6)->textContent;

            dd($playedAt, $type, $result);
        }

        $nextCrawler = new Crawler($domNode);
        // $nextCrawler->filter('()')

        $file = $this->archiveDownload->download($uri, $season);
        if ($file) {
            $sgf = $this->manager->getRepository(BundesligaSgf::class)->findOneBy(['kgsArchivesPath' => $uri]);
            if (!$sgf) {
                $sgf = new BundesligaSgf($uri);
                $this->manager->persist($sgf);
            }
            $sgf->setPath($file);
        }
        $this->manager->flush();
    }

//
//    const BUFFER = 1024;
//
//    function download($remoteFile, $localFile) {
//        $fremote = fopen($remoteFile, 'rb');
//        if (!$fremote) {
//            return false;
//        }
//
//        $flocal = fopen($localFile, 'wb');
//        if (!$flocal) {
//            fclose($fremote);
//            return false;
//        }
//
//        while ($buffer = fread($fremote, BUFFER)) {
//            fwrite($flocal, $buffer);
//        }
//
//        fclose($flocal);
//        fclose($fremote);
//
//        return true;
//    }
//
//download(
//'https://raw.githubusercontent.com/petehouston/php-tips/master/README.md',
//'README.md'
//);
}
