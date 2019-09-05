<?php
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

namespace App\Controller\Helper;

/**
 * Gives you the next meeting date for the club. Club meeting is always on the second monday of a month. If meeting date
 * is over, the next date (next month) is given. For testing you can set dates using the setters.
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class NextClubMeeting
{
    const FORMAT = 'Y-m-d';

    /**
     * @var false|string
     */
    protected $today;

    /**
     * @var string
     */
    protected $monthYear;

    /**
     * @var string
     */
    protected $nextMeetingDate;

    /**
     * NextClubMeeting constructor.
     */
    public function __construct()
    {
        $this->monthYear = date('F Y');
        $this->today = date(self::FORMAT);
    }

    /**
     * @return string
     */
    public function getMonthYear(): string
    {
        return $this->monthYear;
    }

    /**
     * @param string $monthYear Expecting a string of month and year eg. December 2019
     *
     * @return NextClubMeeting
     */
    public function setMonthYear(string $monthYear): self
    {
        $this->monthYear = $monthYear;

        return $this;
    }

    /**
     * @return string Result of the calculation of the next meeting date eg. 2019-06-15
     */
    public function getNextMeetingDate(): string
    {
        return $this->nextMeetingDate;
    }

    /**
     * @return string
     */
    public function getToday(): string
    {
        return $this->today;
    }

    /**
     * @param string $today date string in format YYYY-MM-DD eg 2019-06-23
     *
     * @return NextClubMeeting
     *
     * @throws \Exception
     */
    public function setToday(string $today): NextClubMeeting
    {
        $userDate = new \DateTime($today);
        $this->today = $userDate->format(self::FORMAT);

        return $this;
    }

    /**
     * Gives you a locale string of the calculated date.
     *
     * @param string $actualMonthDate
     *
     * @return string
     */
    public function getLocaleDate(string $actualMonthDate): string
    {
        $timestamp = strtotime($actualMonthDate);

        //set date locale to German
        setlocale(LC_TIME, 'de_DE.utf8');

        return strftime('%e.%B', $timestamp);
    }

    /**
     * @return string German date string eg. 9. September
     */
    public function calcNextMeetingDate(): string
    {
        //second monday of actual month
        $timestamp = $this->createMondayTimestamp($this->monthYear);
        $actualMonthDate = date(self::FORMAT, $timestamp);

        if ($this->getToday() > $actualMonthDate) {
            $nextMonth = $this->calcNextMonth($actualMonthDate);
            $timestamp = $this->createMondayTimestamp($nextMonth);
            $actualMonthDate = date(self::FORMAT, $timestamp);
        }
        $this->nextMeetingDate = $actualMonthDate;

        return $this->getLocaleDate($actualMonthDate);
    }

    /**
     * Creates a timestamp for the second monday of given month and year.
     *
     * @param string $monthYear String in format F Y eg. November 2019
     *
     * @return int
     */
    private function createMondayTimestamp(string $monthYear): int
    {
        return strtotime(sprintf('second monday of %s', $monthYear));
    }

    /**
     * @param string $actualMonthDate
     *
     * @return string
     */
    private function calcNextMonth(string $actualMonthDate)
    {
        $dateTime = \DateTime::createFromFormat(self::FORMAT, $actualMonthDate);
        $dateTime->modify('+1 month');

        return $dateTime->format('F Y');
    }
}
