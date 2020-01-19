<?php

declare(strict_types=1);
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2020 Dr. Holger Maerz
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

namespace App\Tools;

use App\Entity\SgfFile;

class SgfGameInfo
{
    private const RESULT = '#RE\[(.*?)\]#';
    private const PLAY_DATE = '#DT\[(.*?)\]#';
    private const PLACE = '#PC\[(.*?)\]#';
    private const WHITE = '#PW\[(.*?)\]#';
    private const BLACK = '#PB\[(.*?)\]#';
    private const KOMI_POINTS = '#KM\[(.*?)\]#';
    private const HANDICAP = '#HA\[(.*?)\]#';
    private const BOARD_SIZE = '#SZ\[(.*?)\]#';

    public function read(string $file, SgfFile $sgf)
    {
        /** @var resource $handle */
        $handle = fopen($file, 'r');
        while (!feof($handle)) {
            $lineRead = fgets($handle);

            $date = $this->grepPattern(self::PLAY_DATE, $lineRead);
            if (null !== $date) {
                $matches = explode(',', $date);
                $playedAt = \DateTime::createFromFormat('Y-m-d', $matches[0]);

                $sgf->setPlayedAt($playedAt);
                if (count($matches) > 1) {
                    $endAt = \DateTime::createFromFormat('Y-m-d', $matches[1]);
                    $sgf->setPlayedAt($endAt);
                }
            }

            $result = $this->grepPattern(self::RESULT, $lineRead);
            if (null !== $result) {
                $sgf->setResult($result);
                dd($sgf);
            }

            $place = $this->grepPattern(self::PLACE, $lineRead);
            if (null !== $place) {
                $sgf->setPlace($place);
            }

            $white = $this->grepPattern(self::WHITE, $lineRead);
            if (null !== $white) {
                $sgf->setWhite($white);
            }

            $black = $this->grepPattern(self::BLACK, $lineRead);
            if (null !== $black) {
                $sgf->setBlack($black);
            }

            $size = $this->grepPattern(self::BOARD_SIZE, $lineRead);
            if (null !== $size) {
                $sgf->setSize($size);
            }

            $komi = $this->grepPattern(self::KOMI_POINTS, $lineRead);
            if (null !== $komi) {
                $sgf->setKomi($komi);
            }

            $handicap = $this->grepPattern(self::HANDICAP, $lineRead);
            if (null !== $handicap) {
                $sgf->setHandicap($handicap);
            }
        }
        fclose($handle);
    }

    private function grepPattern(string $pattern, string $lineRead): ?string
    {
        $res = preg_match($pattern, $lineRead, $matches);
        if (false === $res) {
            $msg = sprintf('Unexpected Error on pattern: <%s>!', $pattern);
            throw new \LogicException($msg);
        }
        if (0 === $res) {
            return null;
        }

        return $matches[1];
    }
}
