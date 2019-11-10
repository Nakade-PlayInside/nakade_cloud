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

use App\Tools\DGoB\Model\PlayerModel;

class NameCatcher
{
    //eg Darius Dobranis 2d or Sven Gusbert (10k)
    const PATTERN = '#^(.*)(\s\(?\d{1,2}[dDkKp]\)?)#';

    public function extract(string $field): ?PlayerModel
    {
        $name = $field;
        $res = preg_match(self::PATTERN, $field, $matches);
        if (false === $res) {
            throw new \LogicException(sprintf('Expected pattern in field "%s" not found ', $field));
        }

        if (1 === $res) {
            $name = trim($matches[1]);
        }

        $parts = explode(' ', $name);

        //kampflos
        if (count($parts) < 2) {
            return null;
        }

        $lastName = array_pop($parts);
        $pos = strpos($lastName, '(');
        if ($pos > 0) {
            $lastName = substr($lastName, 0, $pos);
        }

        $firstName = array_shift($parts);

        while ($parts) {
            $firstName .= array_shift($parts);
        }

        return new PlayerModel($firstName, $lastName);
    }
}
