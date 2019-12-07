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
use App\Tools\DGoB\Model\KGSIdModel;

class KGSidCatcher
{
    use GrabberLoggerTrait;

    const KGS_PATTERN = 'kgsid:';

    public function extract(string $field): ?KGSIdModel
    {
        $model = new KGSIdModel($field);

        $pos = stripos($field, self::KGS_PATTERN);
        if (false === $pos) {
            $this->logger->notice('No KGS Id found. {field}', ['field' => $field]);

            return null;
        }

        //Shouto1..4 gegen RNT1 - 4
        //BambusBo1..4 - Kassel1..4
        //Hansa21..4/Lion1..4
        //FR2Brett1...4 - Fuerth1..4
        //SteinFux11..14 - Nakade01..04
        $kgsIds = trim(substr($field, $pos + (strlen(self::KGS_PATTERN))));

        //fixing too many dots
        $kgsIds = str_replace('...', '..', $kgsIds);

        //fixing boards
        $kgsIds = str_replace('1 - 4', '1..4', $kgsIds);

        //fixing opposing sign
        $kgsIds = str_replace('gegen', '-', $kgsIds);
        $kgsIds = str_replace(' - ', '/', $kgsIds);

        //removing board numbers
        $kgsIds = str_replace('1..', '', $kgsIds);
        $kgsIds = str_replace('4', '', $kgsIds);

        $matches = explode('/', $kgsIds);

        if (2 !== count($matches)) {
            $this->logger->warning('Wrong delimiter in KGS Id found. {field}', ['field' => $kgsIds]);

            return $model;
        }

        $model->homeId = $this->removeTrailing(array_shift($matches));
        $model->awayId = $this->removeTrailing(array_shift($matches));

        return $model;
    }

    private function removeTrailing(string $kgsId): string
    {
        $kgsId = str_replace('0', '', $kgsId);
        $kgsId = str_ireplace('Brett', '', $kgsId);

        return $kgsId;
    }
}
