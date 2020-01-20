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

namespace App\Tools\Bundesliga;

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaSeason;
use App\Entity\Bundesliga\BundesligaTable;
use App\Logger\GrabberLoggerTrait;
use App\Services\Model\ResultsModel;
use App\Services\Snoopy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Crawls the dom nodes of the DGoB sites for the table of the actual results.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ResultsCatcher
{
    use GrabberLoggerTrait;

    const DGOB_URI = 'http://www.dgob-neu.de/lmo/lmo.php';
    const SEASON_PATTERN = '#^20(\d{2})_20(\d{2})#';
    const DEFAULT_PARAM = '?action=results&tabtype=0';
    const CSS_SELECTOR = 'table.lmoInner';
    const NODE_ROW = 'tr';
    const NODE_CELL = 'td';

    private $snoopy;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->snoopy = new Snoopy();
        $this->manager = $manager;
    }

    /**
     * @return BundesligaTable[]|null
     */
    public function extract(BundesligaSeason $actualSeason, string $matchDay): ?array
    {
        $season = $actualSeason->getDGoBIndex();
        $league = $actualSeason->getLeague();

        //http://www.dgob.de/lmo/lmo.php?action=results&tabtype=0&file=Saison_2013_2014/1314_bl2.l98&st=3
        $linkParams = $this->createLinkParams($season, $league, $matchDay);

        $this->snoopy->fetch(self::DGOB_URI.$linkParams);
        $html = $this->snoopy->results;

        $crawler = new Crawler($html);

        //first lmoInner table!
        $domNode = $crawler->filter(self::CSS_SELECTOR)->getNode(0);
        //return empty array if there are no data: this matchDay is not yet played
        if (!$domNode) {
            $this->logger->error('No dom node found on css {css}', ['css' => self::CSS_SELECTOR]);

            return null;
        }
        $cellCatcher = new ResultCellCatcher($season, $league, $matchDay, $this->logger);
        $data = [];

        //all rows
        $trCrawler = new Crawler($domNode->childNodes);
        /** @var DOMNode $rowNode */
        $iterator = $trCrawler->getIterator();
        foreach ($iterator as $rowNode) {
            if (self::NODE_ROW !== $rowNode->nodeName || !$rowNode->hasChildNodes() || self::NODE_CELL !== $rowNode->firstChild->nodeName) {
                continue;
            }
            if ($rowNode->childNodes->count() < 8) {
                continue;
            }
            $model = $cellCatcher->extract($rowNode->childNodes);

            if ($model) {
                $results = $this->findResults($actualSeason, $model);

                if ($results) {
                    $results->setBoardPointsHome((int) $model->homePoints);
                    $results->setBoardPointsAway((int) $model->awayPoints);
                    $data[] = $results;
                }
            }
        }

    //    $this->manager->flush();

        return $data;
    }

    //prevents unique constraint exception
    private function findResults(BundesligaSeason $actualSeason, ResultsModel $model)
    {
        return $this->manager->getRepository(BundesligaResults::class)->findPairingUnplayed($actualSeason, $model);
    }

    private function createLinkParams(string $season, string $league, string $matchDay)
    {
        if (false === preg_match(self::SEASON_PATTERN, $season, $matches)) {
            throw new \LogicException('Unexpected season format "%s"!', $season);
        }
        $seasonParam = $matches[1].$matches[2];

        $leagueParam = sprintf('%s_bl%s', $seasonParam, $league);
        $linkParam = sprintf('&file=%s.l98&st=%s', $leagueParam, $matchDay);

        return self::DEFAULT_PARAM.$linkParam;
    }
}
