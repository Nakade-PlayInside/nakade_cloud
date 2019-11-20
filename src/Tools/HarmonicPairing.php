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

namespace App\Tools;

/**
 * This pairing algorithm is based on the key index system of the harmonic key scheme of the DFB (11.06.2003), see
 * http://portal.dfbnet.org/fileadmin/content/downloads/faq/SZ_1-L.pdf , commonly known as sliding system
 * (dt. <Rutsch Algorithmus>).
 * The sliding system procedure comes clear if shown graphically (https://de.wikipedia.org/wiki/Spielplan_(Sport)) .
 * This algorithm is considering playing each opponent just once a season. Further more the usage of a bye for impair
 * amount of participants is adopted. Therefore, the balance of color (black or white) is considered.
 * It was sucessfully tested for many years for several leagues.
 * KNOWN ISSUE: none.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class HarmonicPairing
{
    const JOKER = 'JOKER';
    private $original;

    /**
     * Get pairings of players of a single league. Index of the returned array is the number of match day.
     */
    public function getPairings(array $participants, $secondLeg = false): array
    {
        $pairing = [];
        shuffle($participants);
        $this->original = $pairing;

        //set joker to get a pair amount
        if (count($participants) % 2) {
            array_push($participants, self::JOKER);
        }

        $noParticipants = count($participants);
        $firstLegRounds = $noParticipants;
        if ($secondLeg) {
            $noParticipants *= 2;
            --$noParticipants;
        }
        for ($round = 1; $round < $noParticipants; ++$round) {
            $firstLeg = $round < $firstLegRounds;
            $pairing[$round] = $this->createPairing($participants, $firstLeg);
            $this->moveLastToSecond($participants);
        }

        return $pairing;
    }

    /**
     * League system s. https://de.wikipedia.org/wiki/Spielplan_(Sport).
     */
    private function moveLastToSecond(array &$participants)
    {
        $first = $participants[1];
        $last = array_pop($participants);

        //set last on first place followed by previous first (now second)
        array_splice($participants, 1, 1, [$last, $first]);
    }

    /**
     * @return array
     */
    private function createPairing(array $participants, bool $firstLeg)
    {
        $roundPairings = [];
        while (count($participants) > 0) {
            $home = array_shift($participants);
            $away = array_pop($participants);
            $match = $this->createHomeMatch($home, $away, $firstLeg);

            if (in_array(self::JOKER, $match)) {
                continue;
            }
            $roundPairings[] = $match;
        }

        return $roundPairings;
    }

    private function createHomeMatch($home, $away, $firstLeg)
    {

        $homeKey = array_search($home, $this->original);
        $awayKey = array_search($away, $this->original);
        $sum = $homeKey + $awayKey;

        //pair amount
        if (0 === $sum % 2) {
            $match = [$away, $home];
            if ($homeKey > $awayKey) {
                $match = array_reverse($match);
            }
        } else {
            $match = [$home, $away];
            if ($homeKey > $awayKey) {
                $match = array_reverse($match);
            }
        }

        if (!$firstLeg) {
            $match = array_reverse($match);
        }

        return $match;
    }
}
