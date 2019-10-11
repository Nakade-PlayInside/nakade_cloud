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
 * Gives you the next meeting date for the club. Club meeting is always on the second monday of a month. If meeting date
 * is over, the next date (next month) is given.
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class NextClubMeeting
{
    const DATE_FORMAT = 'Y-m-d';
    private const MONTH_YEAR_FORMAT = 'F Y';

    /**
     * @return string a date string in format Y-m-d eg. 2019-09-17
     */
    public function calcNextMeetingDate(): string
    {
        //second monday of actual month
        $monthYear = date(self::MONTH_YEAR_FORMAT);
        $today = date(self::DATE_FORMAT);
        $timestamp = $this->createMondayTimestamp($monthYear);
        $nextMeetingDate = date(self::DATE_FORMAT, $timestamp);

        if ($today > $nextMeetingDate) {
            $nextMonth = $this->calcNextMonth($nextMeetingDate);
            $timestamp = $this->createMondayTimestamp($nextMonth);
            $nextMeetingDate = date(self::DATE_FORMAT, $timestamp);
        }

        return $nextMeetingDate;
    }

    /**
     * Creates a timestamp for the second monday of given month and year.
     */
    private function createMondayTimestamp(string $monthYear): int
    {
        return strtotime(sprintf('second monday of %s', $monthYear));
    }

    private function calcNextMonth(string $actualMonthDate): string
    {
        $dateTime = \DateTime::createFromFormat(self::DATE_FORMAT, $actualMonthDate);
        $dateTime->modify('+1 month');

        return $dateTime->format(self::MONTH_YEAR_FORMAT);
    }
}
