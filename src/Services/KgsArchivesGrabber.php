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
use App\Tools\KgsArchives\KgsCellReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

class KgsArchivesGrabber
{
    //https://www.gokgs.com/gameArchives.jsp?user=nakade01&year=2012&month=9
    //erste tabelle class grid
    //erste spalte = http://files.gokgs.com/games/2012/9/13/AGruKi1-Nakade01.sgf
    //spalte Typ != Besprechung   type==frei || gewertet?

    private $archiveDownload;
    private $manager;
    private $contentRetriever;
    private $kgsCellReader;

    public function __construct(EntityManagerInterface $manager, KgsArchivesDownload $download, KgsCellReader $kgsCellReader)
    {
        $this->archiveDownload = $download;
        $this->manager = $manager;
        $this->contentRetriever = new ContentRetriever();
        $this->kgsCellReader = $kgsCellReader;
    }

    public function extract()
    {
        $uri = 'http://files.gokgs.com/games/2012/9/13/AGruKi1-Nakade01.sgf';
        $season = '2012_13';

        $html = $this->contentRetriever->grab('https://www.gokgs.com/gameArchives.jsp?user=nakade01&year=2012&month=9');
        $crawler = new Crawler($html);
        //first table!
        $domNode = $crawler->filter('table.grid')->getNode(0);
        // seven cells expected
        if (!$domNode || 7 !== $domNode->firstChild->childNodes->length) {
            return;
        }

        /** @var \DOMNode $row */
        foreach ($domNode->childNodes as $row) {
            if ('th' === $row->firstChild->nodeName) {
                continue;
            }
            $model = $this->kgsCellReader->extract($row);

            //downloadLink is uri
            dd($model);
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
            $sgf->setPath($file)
                ->setPlayedAt($model->playedAt);
        }
        $this->manager->flush();
    }
}
