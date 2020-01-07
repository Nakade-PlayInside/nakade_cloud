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

namespace App\Tools\KgsArchives;

use App\Logger\KgsArchivesLoggerTrait;
use App\Services\Model\KgsArchivesModel;
use Symfony\Component\DomCrawler\Crawler;

class KgsCellReader
{
    use KgsArchivesLoggerTrait;

    private const TABLE_CELL = 'td';
    private const LINK_TAG = 'a';

    public function extract(\DOMNode $tableRow): ?KgsArchivesModel
    {
        $crawler = new Crawler($tableRow);
        $model = new KgsArchivesModel();

        if (!$tableRow->hasChildNodes()) {
            $this->logger->critical('No data found!');

            return null;
        }

        //review has colspan ; just 6 cells
        if ($tableRow->childNodes->length !== 7) {
            return null;
        }

        $linkNode = $crawler->filter(self::TABLE_CELL)->getNode(0);
        //no download
        if (!$linkNode) {
            $this->logger->critical('No sgf download link found!');

            return null;
        }
        $downloadLink = $this->getDownloadLink($linkNode);
        if ($downloadLink) {
            $this->logger->notice(sprintf('Found sgf download link: %s', $downloadLink));

            $model->downloadLink = $downloadLink;
        }

        $dateNode = $crawler->filter(self::TABLE_CELL)->getNode(4);
        if ($dateNode) {
            $playedAt = $this->convertTimezone($dateNode->textContent);
            $this->logger->notice(sprintf('Converted local date: %s', $playedAt->format('d.m.Y H:i')));

            $model->playedAt = $playedAt;
        }

        $typeNode = $crawler->filter(self::TABLE_CELL)->getNode(5);
        if ($typeNode) {
            $type = $typeNode->textContent;
            $this->logger->notice(sprintf('Match type found: %s', $type));

            $model->type = $type;
        }

        $resultNode = $crawler->filter(self::TABLE_CELL)->getNode(6);
        if ($resultNode) {
            $result = $resultNode->textContent;
            $this->logger->notice(sprintf('Result found: %s', $result));
            if (0 === strcasecmp($result, 'unfinished')) {
                $this->logger->alert(sprintf('Result skipped.'));

                return null;
            }

            $model->result = $result;
        }

        return $model;
    }

    private function getDownloadLink(\DOMNode $node): ?string
    {
        $crawler = new Crawler($node);
        $attr = $crawler->filter(self::LINK_TAG)->extract(['href']);
        //no download
        if (empty($attr)) {
            $this->logger->critical('No href attribute found!');

            return null;
        }

        return array_shift($attr);
    }

    /**
     * Converts GMT time string to CET DateTime object.
     */
    private function convertTimezone(string $matchDate): \DateTimeInterface
    {
        $playedAt = new \DateTime($matchDate, new \DateTimeZone('GMT'));
        $this->logger->notice(sprintf('Found GMT date: %s', $playedAt->format('d.m.Y H:i')));

        return $playedAt->setTimezone(new \DateTimeZone('Europe/Berlin'));
    }
}
