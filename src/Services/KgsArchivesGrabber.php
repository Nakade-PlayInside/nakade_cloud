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
use App\Entity\Bundesliga\MatchInterface;
use App\Services\Model\KgsArchivesModel;
use App\Tools\KgsArchives\KgsCellReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

class KgsArchivesGrabber
{
    //https://www.gokgs.com/gameArchives.jsp?user=nakade01&year=2012&month=9
    //erste tabelle class grid
    //erste spalte = http://files.gokgs.com/games/2012/9/13/AGruKi1-Nakade01.sgf
    //spalte Typ != Besprechung   type==frei || gewertet?

    private const TABLE_HEAD = 'th';
    private const CSS_SELECTOR = 'table.grid';
    private const ARCHIVE_URI = 'https://www.gokgs.com/gameArchives.jsp';

    private $archiveDownload;
    private $manager;
    private $contentRetriever;
    private $kgsCellReader;
    private $uploadDir;

    public function __construct(EntityManagerInterface $manager, KgsArchivesDownload $download, KgsCellReader $kgsCellReader, KernelInterface $appKernel)
    {
        $this->archiveDownload = $download;
        $this->manager = $manager;
        $this->contentRetriever = new ContentRetriever();
        $this->kgsCellReader = $kgsCellReader;
        $this->uploadDir = $appKernel->getProjectDir().'/public';
    }

    public function extract(MatchInterface $match)
    {
        //'http://files.gokgs.com/games/2012/9/13/AGruKi1-Nakade01.sgf';
        $link = $this->createLink($match);
        $html = $this->contentRetriever->grab($link);

        $crawler = new Crawler($html);

        //first table!
        $domNode = $crawler->filter(self::CSS_SELECTOR)->getNode(0);
        //excluding review
        if (!$domNode || 7 !== $domNode->firstChild->childNodes->length) {
            return;
        }

        $entities = [];
        /** @var \DOMNode $row */
        foreach ($domNode->childNodes as $row) {
            if (self::TABLE_HEAD === $row->firstChild->nodeName) {
                continue;
            }
            //commented and unfinished games are excluded
            $model = $this->kgsCellReader->extract($row);
            if ($model && $model->downloadLink) {
                //SEASON DIR
                $saveDir = $match->getSeason()->getSgfDir();
                $localPath = $this->archiveDownload->download($model->downloadLink, $saveDir);
                if ($localPath) {
                    $entities[] = $this->makeEntity($model, $localPath);
                    //we have to assign the sgf files to a match solving a problem
                    //occasionally, there are more than one match per month which will break the one to one relation
                    //unfortunately, we cannot always assign by date since some matches are played prior than common play date.
                }
            }
        }
        //only one sgf found in month ; easy to assign
        if (1 === count($entities)) {
            $sgf = array_shift($entities);
            $match->setSgf($sgf);
        }
        //more than one match per month
        if (count($entities) > 1) {
            /** @var BundesligaSgf $sgf */
            foreach ($entities as $sgf) {
                //match played at usual league play date
                if ($match->getResults()->getPlayedAt()->format('d.m.Y') === $sgf->getPlayedAt()->format('d.m.Y')) {
                    $match->setSgf($sgf);
                    continue;
                }
                //match was played prior but this works for matches since Dec 2019 only
                if ($match->getTargetDate() && $match->getTargetDate()->format('d.m.Y') === $sgf->getPlayedAt()->format('d.m.Y')) {
                    $match->setSgf($sgf);
                }
            }
        }

        $this->manager->flush();
    }

    private function createLink(MatchInterface $match): string
    {
        /** @var \DateTimeInterface $date */
        $date = $match->getResults()->getPlayedAt();

        return self::ARCHIVE_URI.sprintf(
            '?user=nakade0%s&year=%s&month=%s',
            $match->getBoard(),
            $date->format('Y'),
            $date->format('n')
        );
    }

    private function makeEntity(KgsArchivesModel $model, string $localPath): ?BundesligaSgf
    {
        $path = $this->uploadDir.'/'.$localPath;
        $hash = md5_file($path);
        $sgf = $this->manager->getRepository(BundesligaSgf::class)->findOneBy(['hash' => $hash]);

        if (!$sgf) {
            $sgf = new BundesligaSgf($model->downloadLink);

            $sgf->setPath($localPath)
                    ->setPlayedAt($model->playedAt)
                    ->setType($model->type)
                    ->setResult($model->result)
                    ->setHash($hash)
            ;

            $this->manager->persist($sgf);
        }

        return $sgf;
    }
}
