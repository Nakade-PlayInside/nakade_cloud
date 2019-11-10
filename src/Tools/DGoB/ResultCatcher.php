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

use App\Tools\DGoB\Model\ResultModel;
use App\Tools\DGoB\Model\TeamModel;
use Symfony\Component\DomCrawler\Crawler;

class ResultCatcher
{
    const SELECTOR = 'td';

    private $rowCrawler;

    public function __construct(\DOMNode $node)
    {
        $this->rowCrawler = new Crawler($node);
    }

    public function extract(): ?ResultModel
    {
        //content rows have 10 cells
        if ($this->rowCrawler->filter(self::SELECTOR)->count() < 10) {
            return null;
        }

        $date = $this->getTextContent(0);
        $home = $this->getTextContent(2);
        $away = $this->getTextContent(4);
        $homePoints = $this->getTextContent(6);
        $awayPoints = $this->getTextContent(8);
        $details = $this->getTextContent(10);

        $resultModel = $this->createResultModel($home, $away, $date, $homePoints, $awayPoints);
        $this->extractDetails($details, $resultModel);

        return $resultModel;
    }

    private function getTextContent(int $position): string
    {
        return $this->rowCrawler->filter(self::SELECTOR)->getNode($position)->textContent;
    }

    private function createResultModel(string $home, string $away, string $date, string $homePoints, string $awayPoints): ResultModel
    {
        $homeTeam = new TeamModel($home);
        $awayTeam = new TeamModel($away);
        $resultModel = new ResultModel($homeTeam, $awayTeam, $date);

        $resultModel->boardPointsHome = $homePoints;
        $resultModel->boardPointsAway = $awayPoints;

        return $resultModel;
    }

    private function extractDetails(string $details, ResultModel &$result)
    {
        $pos = strpos($details, 'Notiz:');
        if (false === $pos) {
            return;
        }
        $details = substr($details, $pos);
        // 'Notiz: \n\nKGSid: Unibonn1..4/Nakade01..04 \n\n2:0 (s) Christian Kuehner 2d - Matthias Knoepke 1d \n0:2 ...

        $matchData = (explode('\n', $details));
        //first match is Notiz:
        unset($matchData[0]);

        $pairingModel = (new PairingsCatcher())->extract($matchData);

        if ($pairingModel) {
            $result->matches = $pairingModel->getMatches();

            if ($pairingModel->getKgsIdModel()) {
                $result->rawKgsId = $pairingModel->getKgsIdModel()->rawKgsId;
                $result->homeTeam->kgsId = $pairingModel->getKgsIdModel()->homeId;
                $result->awayTeam->kgsId = $pairingModel->getKgsIdModel()->awayId;
            }
        }
    }
}
