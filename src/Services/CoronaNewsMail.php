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

namespace App\Services;

use App\Entity\CoronaNews;
use App\Entity\NewsReader;
use App\Tools\DeliveryDateChecker;
use App\Tools\NewsSendDate;
use App\Tools\NextWeeklyMeeting;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class CoronaNewsMail
{
    const TIME_SPAN = 2;

    private $deliverer;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, CoronaNewsDeliverer $deliverer)
    {
        $this->deliverer = $deliverer;
        $this->entityManager = $entityManager;
    }

    public function handle()
    {
        $dueDate = (new NextWeeklyMeeting())->calcNextMeetingDate();

        //news was already sent
        $newsletter = $this->entityManager->getRepository(CoronaNews::class)->findOneBy(['dueAt' => $dueDate]);
        if ($newsletter) {
            return;
        }

        $sendDate = (new NewsSendDate())->create($dueDate, self::TIME_SPAN);
        if ((new DeliveryDateChecker())->isDelayed($sendDate)) {
            $allReaders = $this->entityManager->getRepository(NewsReader::class)->findAll();
            if (0 === count($allReaders)) {
                return;
            }

            //entity
            $newsletter = new CoronaNews($dueDate, count($allReaders));
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();

            //send news
            $this->deliverer->deliver($dueDate, $allReaders);
        }
    }
}
