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

namespace App\Twig\Helper;

/**
 * Gives you an alert message if meeting is tomorrow or today.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class MeetingAlert
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
     * Gives you an alert HTML if date is today or tomorrow.
     *
     * @param string $nextMeetingDate expects a date string in format 'Y-m-d eg. 2019-11-24
     *
     * @return string
     */
    public function getAlert(string $nextMeetingDate): string
    {
        $this->nextMeetingDate = $nextMeetingDate;
        $this->setDayBeforeMeeting($nextMeetingDate);
        $this->today = date(self::FORMAT);

        //tomorrow
        if (strtotime($this->today) === strtotime($this->dayBeforeMeeting)) {
            return 'Morgen';
        }
        //today
        if (strtotime($this->today) === strtotime($this->nextMeetingDate)) {
            return 'Heute';
        }

        return '';
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
