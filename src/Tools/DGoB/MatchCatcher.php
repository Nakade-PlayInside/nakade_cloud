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
use App\Tools\DGoB\Model\MatchModel;

class MatchCatcher
{
    use GrabberLoggerTrait;

    const PATTERN = '#^([012]:[012])\s\((.)\)\s(.*)\s-\s(.*)#';

    private $nameCatcher;

    public function __construct()
    {
        $this->nameCatcher = new NameCatcher();
    }

    public function extract(string $field): ?MatchModel
    {
        //correction
        $field = str_replace('!', '', $field);
        $field = str_replace('_', '0', $field);

        $result = preg_match(self::PATTERN, $field, $matches);
        if (false === $result) {
            throw new \LogicException(sprintf('Expected pattern in field "%s" not found ', $field));
        }

        if (0 === $result) {
            $this->logger->error('No result found: {field}!', ['field' => $field]);

            return null;
        }

        $result = $matches[1];
        $color = $matches[2];
        $model = new MatchModel($color, $result);

        $homePlayer = trim($matches[3]);
        $awayPlayer = trim($matches[4]);

        $playerHome = $this->nameCatcher->extract($homePlayer);
        if ($playerHome) {
            $playerHome->color = $color;
            $model->homePlayer = $playerHome;

            $this->logger->info('Home player found: {data}.', ['data' => $playerHome]);
        }

        $playerAway = $this->nameCatcher->extract($awayPlayer);
        if ($playerAway) {
            $playerAway->color = ('w' === $color ? 'b' : 'w');
            $model->awayPlayer = $playerAway;

            $this->logger->info('Away player found: {data}.', ['data' => $playerAway]);
        }

        if (!$playerHome || !$playerAway) {
            $this->logger->notice(
                'No players but result found: {field}. Set to win by default!',
                ['field' => $field]
            );

            $model->winByDefault = true;
        }

        return $model;
    }
}
