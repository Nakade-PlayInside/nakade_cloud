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
use Symfony\Component\DomCrawler\Crawler;

class MatchDayCatcher
{
    use GrabberLoggerTrait;

    const CSS_SELECTOR = 'table.lmoInner';

    private $resultCatcher;

    public function __construct(ResultCatcher $resultCatcher)
    {
        $this->resultCatcher = $resultCatcher;
    }

    public function extract(Crawler $domCrawler, string $matchDay): array
    {
        $results = [];
        //find the result table node
        $tableNodes = $domCrawler->filter(self::CSS_SELECTOR)->getNode(0);
        if (!$tableNodes || !$tableNodes->hasChildNodes()) {
            $this->logger->error('No node or child nodes found by class selector.');

            return $results;
        }

        /** @var \DOMNode $childNode */
        foreach ($tableNodes->childNodes as $childNode) {
            $rowCrawler = new Crawler($childNode);
            $resultModel = $this->resultCatcher->extract($rowCrawler);
            if (!$resultModel) {
                continue;
            }
            $resultModel->matchDay = $matchDay;
            $results[] = $resultModel;
        }
        $this->logger->info(
            'Number of results {results} found on match day {day}.',
            ['results' => count($results), 'day' => $matchDay]
        );

        return $results;
    }
}
