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
 * Gives you an alert message if club meeting is tomorrow or today.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ClubMeetingAlert
{
    const FORMAT = 'Y-m-d';

    /**
     * @var false|string
     */
    protected $today;

    /**
     * @var string
     */
    protected $nextMeetingDate;

    /**
     * @var string
     */
    protected $dayBeforeMeeting;

    /**
     * ClubMeetingAlert constructor.
     *
     * @param string $nextMeetingDate
     */
    public function __construct(string $nextMeetingDate = '')
    {
        $this->nextMeetingDate = $nextMeetingDate;
        $this->setDayBeforeMeeting($nextMeetingDate);
        $this->today = date(self::FORMAT);
    }

    /**
     * @return false|string
     */
    public function getToday()
    {
        return $this->today;
    }

    /**
     * @return string
     */
    public function getNextMeetingDate(): string
    {
        return $this->nextMeetingDate;
    }

    /**
     * @return string
     */
    public function getDayBeforeMeeting(): string
    {
        return $this->dayBeforeMeeting;
    }

    /**
     * @return bool
     */
    public function isToday(): bool
    {
        return strtotime($this->today) == strtotime($this->nextMeetingDate);
    }

    /**
     * @return bool
     */
    public function isTomorrow(): bool
    {
        return strtotime($this->today) == strtotime($this->dayBeforeMeeting);
    }

    /**
     * @param string $nextMeetingDate
     *
     * @return string
     */
    private function setDayBeforeMeeting(string $nextMeetingDate)
    {
        $dateTime = \DateTime::createFromFormat(self::FORMAT, $nextMeetingDate);
        $dateTime->modify('-1 day');

        $this->dayBeforeMeeting = $dateTime->format(self::FORMAT);
    }
}
