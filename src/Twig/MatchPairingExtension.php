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

namespace App\Twig;

use App\Entity\Bundesliga\BundesligaMatch;

class MatchPairingExtension extends \Twig_Extension
{
    const WIN_BY_DEFAULT = 'kampflos!';
    const BLACK = 's';
    const WHITE = 'w';

    public function getFilters()
    {
        return [
                new \Twig_SimpleFilter(
                    'pairing',
                    [$this, 'getMatchPairing']
                ),
        ];
    }

    public function getMatchPairing(BundesligaMatch $match)
    {
        //result
        $result = $match->getResult();

        $players = [];
        if ($match->getPlayer()) {
            $players[] = $match->getPlayer()->getName();
        } else {
            $players[] = self::WIN_BY_DEFAULT;
        }

        if ($match->getOpponent()) {
            $players[] = $match->getOpponent()->getName();
        } else {
            $players[] = self::WIN_BY_DEFAULT;
        }

        if (!$match->isHomeMatch()) {
            $result = strrev($result);
            $players = array_reverse($players);
        }

        $color = self::WHITE === $match->getColor() ? self::WHITE : self::BLACK;
        $home = array_shift($players);
        $away = array_pop($players);

        $pairing = sprintf('%s (%s) %s - %s', $result, $color, $home, $away);
        if (false === stripos($pairing, self::WIN_BY_DEFAULT) && $match->isWinByDefault()) {
            $pairing .= self::WIN_BY_DEFAULT;
        }

        return $pairing;
    }
}
