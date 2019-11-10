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

use App\Services\Snoopy;
use App\Tools\DGoB\Model\ResultModel;

class SeasonCatcher
{
    CONST DGOB_URI = 'http://www.dgob.de/lmo/lmo.php';

    private $snoopy;
    private $league;

    public function __construct(string $league = '2')
    {
        $this->snoopy = new Snoopy();
        $this->league = $league;
    }

    public function extract()
    {
        $season = '1920'; //season years
        $league = 'bl2';
        //underscoring points means NOT YET PLAYED
        $seasonResults = [];

        $count = 1;
        while (true) {
            $matchDay = "$count";
            $linkParams = sprintf('?action=results&tabtype=0&file=%s_%s.l98&st=%s', $season, $league, $matchDay);
            $this->snoopy->fetch(self::DGOB_URI.$linkParams);
            $html = $this->snoopy->results;

            $results = (new MatchDayCatcher($html))->extract($matchDay);
            if (!$results) {
                break;
            }
            $seasonResults = array_merge($seasonResults, $results);
            ++$count;
        }

        return $seasonResults;
    }
}
