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

namespace App\Form\Model;

use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaResults;

class ResultModel
{
    public $results;
    public $firstBoardMatch;
    public $secondBoardMatch;
    public $thirdBoardMatch;
    public $fourthBoardMatch;

    public function __construct(BundesligaResults $results)
    {
        $this->results = $results;
        $this->initMatches($results);
    }

    public function isNakadeHome(): bool
    {
        return false !== stripos($this->results->getHome()->getName(), 'Nakade');
    }

    private function initMatches(BundesligaResults $results)
    {
        foreach ($results->getMatches() as $match) {
            switch ($match->getBoard()) {
                case 1:
                    $this->firstBoardMatch = $match;
                    break;
                case 2:
                    $this->secondBoardMatch = $match;
                    break;
                case 3:
                    $this->thirdBoardMatch = $match;
                    break;
                case 4:
                    $this->fourthBoardMatch = $match;
                    break;
            }
        }

        if (!$this->firstBoardMatch) {
            $this->firstBoardMatch = $this->createMatch($results, 1);
        }
        if (!$this->secondBoardMatch) {
            $this->secondBoardMatch = $this->createMatch($results, 2);
        }
        if (!$this->thirdBoardMatch) {
            $this->thirdBoardMatch = $this->createMatch($results, 3);
        }
        if (!$this->fourthBoardMatch) {
            $this->fourthBoardMatch = $this->createMatch($results, 4);
        }
    }

    private function createMatch(BundesligaResults $results, int $board)
    {
        $match = new BundesligaMatch();
        $match->setSeason($results->getSeason())
                ->setBoard($board)
                ->setResults($results)
                ->setWinByDefault(true)
                ->setResult('2:0')
        ;
        ;

        //default is black
        if ($match->isHomeMatch() && 0 === $board % 2) {
            $match->setColor('w');
        }
        if (!$match->isHomeMatch() && 1 === $board % 2) {
            $match->setColor('w');
        }

        return $match;
    }
}
