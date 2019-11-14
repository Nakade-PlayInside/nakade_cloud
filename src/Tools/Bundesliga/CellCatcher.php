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

use App\Entity\Bundesliga\BundesligaTable;
use App\Tools\Bundesliga\Model\RowModel;
use Symfony\Component\DomCrawler\Crawler;

class CellCatcher
{
    const TABLE_CELL = 'td';
    const IMG_SRC = 'src';
    const TD_CLASS = 'class';

    private $season;
    private $league;
    private $matchDay;

    public function __construct(string $season, string $league, string $matchDay)
    {
        $this->season = $season;
        $this->league = $league;
        $this->matchDay = $matchDay;
    }

    public function extract(\DOMNodeList $childNodes)
    {
        if ($childNodes->count() < 16) {
            return null;
        }
        $rowCrawler = new Crawler($childNodes);
        $model = new BundesligaTable();
        $model->setSeason($this->season)
            ->setLeague($this->league)
            ->setMatchDay($this->matchDay)
        ;

        $node = $rowCrawler->filter(self::TABLE_CELL)->getNode(0);
        $model->setPosition((int) $node->textContent);

        /** @var $node \DOMElement */
        if ($node->hasAttribute(self::TD_CLASS)) {
            $class = $node->getAttribute(self::TD_CLASS);
            $tendency = $this->getTendency($class);
            $model->setTendency($tendency);
        }

        $imgNode = $rowCrawler->filter(self::TABLE_CELL)->getNode(1);
        $imgSrc = $this->getImgSource($imgNode);
        $model->setImgSrc($imgSrc);

        $name = $rowCrawler->filter(self::TABLE_CELL)->getNode(3)->textContent;
        $team = $this->cleanTeamName($name);
        $model->setTeam($team);

        $games = $rowCrawler->filter(self::TABLE_CELL)->getNode(7)->textContent;
        $model->setGames($games);

        $wins = $rowCrawler->filter(self::TABLE_CELL)->getNode(9)->textContent;
        $model->setWins($wins);

        $draws = $rowCrawler->filter(self::TABLE_CELL)->getNode(10)->textContent;
        $model->setDraws($draws);

        $losses = $rowCrawler->filter(self::TABLE_CELL)->getNode(11)->textContent;
        $model->setLosses($losses);

        $boardPoints = $rowCrawler->filter(self::TABLE_CELL)->getNode(13)->textContent;
        $model->setBoardPoints($boardPoints);

        $points = $rowCrawler->filter(self::TABLE_CELL)->getNode(15)->textContent;
        $model->setPoints($points);

        return $model;
    }

    private function cleanTeamName(string $name)
    {
        $name = str_replace('%0A', '', $name);

        return trim($name);
    }

    private function getTendency(string $class): ?int
    {
        if (false !== stripos($class, 'lmoTabelleMeister')) {
            return  BundesligaTable::TENDENCY_CHAMPION;
        }
        if (false !== stripos($class, 'lmoTabelleCleague')) {
            return BundesligaTable::TENDENCY_AUFSTEIGER;
        }
        if (false !== stripos($class, 'lmoTabelleRelegation')) {
            return BundesligaTable::TENDENCY_RELEGATION;
        }
        if (false !== stripos($class, 'lmoTabelleAbsteiger')) {
            return BundesligaTable::TENDENCY_ABSTEIGER;
        }

        return null;
    }

    private function getImgSource(\DOMNode $node): ?string
    {
        if (!$node->hasChildNodes()) {
            return null;
        }
        /** @var \DOMElement $imgNode */
        $imgNode = $node->firstChild;

        if ($imgNode->hasAttribute(self::IMG_SRC)) {
            return $imgNode->getAttribute(self::IMG_SRC);
        }

        return null;
    }
}
