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

namespace App\Tools\DGoB;

use App\Logger\GrabberLoggerTrait;
use App\Services\Snoopy;
use App\Tools\DGoB\Model\SeasonModel;
use Symfony\Component\DomCrawler\Crawler;

class SeasonCatcher
{
    use GrabberLoggerTrait;

    const DGOB_URI = 'http://www.dgob.de/lmo/lmo.php';
    const SEASON_PATTERN = '#^20(\d{2})_20(\d{2})#';
    const DEFAULT_PARAM = '?action=results&tabtype=0';

    private $snoopy;
    private $catcher;

    public function __construct(MatchDayCatcher $catcher)
    {
        //2015_2016
        $this->snoopy = new Snoopy();
        $this->catcher = $catcher;
    }

    public function extract(string $season, string $league, $isActualSeason = false): SeasonModel
    {
        $this->logger->notice('SeasonCatcher started.');

        //underscoring points means NOT YET PLAYED
        $seasonResults = [];
        $model = new SeasonModel($league);
        $model->title = $this->createTitle($season);
        $this->logger->info('Created season title {title}.', ['title' => $model->title]);

        $matchDay = 1;
        while (true) {
            //http://www.dgob.de/lmo/lmo.php?action=results&tabtype=0&file=Saison_2013_2014/1314_bl2.l98&st=3
            $linkParams = $this->createLinkParams($season, $league, "$matchDay", $isActualSeason);
            $this->snoopy->fetch(self::DGOB_URI.$linkParams);
            $html = $this->snoopy->results;

            $domCrawler = new Crawler($html);
            $results = $this->catcher->extract($domCrawler, "$matchDay");
            if (!$results) {
                $this->logger->info('Left loop on match day {day}.', ['day' => $matchDay]);
                break;
            }
            $seasonResults = array_merge($seasonResults, $results);
            ++$matchDay;
        }
        $model->results = $seasonResults;

        return $model;
    }

    private function createLinkParams(string $season, string $league, string $matchDay, bool $isActualSeason)
    {
        if (false === preg_match(self::SEASON_PATTERN, $season, $matches)) {
            throw new \LogicException('Unexpected season format "%s"!', $season);
        }

        $seasonParam = $matches[1].$matches[2];

        $leagueParam = sprintf('%s_bl%s', $seasonParam, $league);
        $fileParam = sprintf('Saison_%s', $season);

        $linkParam = sprintf('&file=%s/%s.l98&st=%s', $fileParam, $leagueParam, $matchDay);
        if ($isActualSeason) {
            $linkParam = sprintf('&file=%s.l98&st=%s', $leagueParam, $matchDay);
        }
        $this->logger->info('links parameter for request {param}', ['param' => self::DEFAULT_PARAM.$linkParam]);

        return self::DEFAULT_PARAM.$linkParam;
    }

    private function createTitle(string $season)
    {
        //2018_2019 -> Saison 2018/19
        $parts = explode('_', $season);
        $endYear = substr($parts[1], 2);

        return sprintf('Saison %s/%s', $parts[0], $endYear);
    }
}
