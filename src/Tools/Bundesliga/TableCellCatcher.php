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
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2020 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 * @deprecated
 */
class TableCellCatcher
{
    const TABLE_CELL = 'td';
    const IMG_SRC = 'src';
    const TD_CLASS = 'class';

    private $season;
    private $league;
    private $matchDay;
    private $logger;

    public function __construct(string $season, string $league, string $matchDay, LoggerInterface $logger)
    {
        $this->season = $season;
        $this->league = $league;
        $this->matchDay = $matchDay;
        $this->logger = $logger;
    }

    public function extract(\DOMNodeList $childNodes): ?BundesligaTable
    {
        if ($childNodes->count() < 16) {
            $this->logger->error(
                'Less child nodes {nodes} found as expected.',
                ['nodes' => $childNodes->count()]
            );

            return null;
        }
        $rowCrawler = new Crawler($childNodes);
        $model = new BundesligaTable();
        $model->setSeason($this->season)
            ->setLeague($this->league)
            ->setMatchDay($this->matchDay)
        ;

        $node = $rowCrawler->filter(self::TABLE_CELL)->getNode(0);
        if (!$node) {
            $this->logger->error('No table cells TD found.');

            return null;
        }
        $model->setPosition((int) $node->textContent);
        $this->logger->notice('Found data for position {position}.', ['position' => $model->getPosition()]);

        /** @var $node \DOMElement */
        if ($node->hasAttribute(self::TD_CLASS)) {
            $class = $node->getAttribute(self::TD_CLASS);
            $tendency = $this->getTendency($class);
            $model->setTendency($tendency);
        } else {
            $this->logger->error('No tendency found.');
        }

        $imgNode = $rowCrawler->filter(self::TABLE_CELL)->getNode(1);
        $imgSrc = $this->getImgSource($imgNode);
        $model->setImgSrc($imgSrc);
        if (!$imgNode) {
            $this->logger->error('No tendency image found.');
        }

        $name = $rowCrawler->filter(self::TABLE_CELL)->getNode(3)->textContent;
        $team = $this->cleanTeamName($name);
        $model->setTeam($team);
        if (!isset($name)) {
            $this->logger->error('No team name found.');
        }

        $games = $rowCrawler->filter(self::TABLE_CELL)->getNode(7)->textContent;
        $model->setGames($games);
        if (!isset($games)) {
            $this->logger->error('No number of games found.');
        }

        $wins = $rowCrawler->filter(self::TABLE_CELL)->getNode(9)->textContent;
        $model->setWins($wins);
        if (!isset($wins)) {
            $this->logger->error('No number of wins found.');
        }

        $draws = $rowCrawler->filter(self::TABLE_CELL)->getNode(10)->textContent;
        $model->setDraws($draws);
        if (!isset($draws)) {
            $this->logger->error('No number of draws found.');
        }

        $losses = $rowCrawler->filter(self::TABLE_CELL)->getNode(11)->textContent;
        $model->setLosses($losses);
        if (!isset($losses)) {
            $this->logger->error('No number of losses found.');
        }

        $boardPoints = $rowCrawler->filter(self::TABLE_CELL)->getNode(13)->textContent;
        $model->setBoardPoints($boardPoints);
        if (!isset($boardPoints)) {
            $this->logger->error('No number of board points found.');
        }

        $points = $rowCrawler->filter(self::TABLE_CELL)->getNode(15)->textContent;
        $model->setPoints($points);
        if (!isset($points)) {
            $this->logger->error('No number of points found.');
        }

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
            $this->logger->error('Node of img source has no child nodes.');

            return null;
        }
        /** @var \DOMElement $imgNode */
        $imgNode = $node->firstChild;

        if ($imgNode->hasAttribute(self::IMG_SRC)) {
            $imgSrc = $imgNode->getAttribute(self::IMG_SRC);
            if (empty($imgSrc)) {
                return null;
            }
            $parts = pathinfo($imgSrc);

            return $parts['basename'];
        }
        $this->logger->error('No img source found.');

        return null;
    }
}
