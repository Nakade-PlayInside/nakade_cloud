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
namespace App\Command;

use App\Entity\NewsLetter;
use App\Entity\NewsReader;
use App\Services\NewsDeliverer;
use App\Tools\NextClubMeeting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewsDeliverCommand extends Command
{
    protected static $defaultName = 'app:news-deliver';
    private $newsDeliverer;
    private $entityManager;
    private $nextClubMeeting;

    public function __construct(EntityManagerInterface $entityManager, NewsDeliverer $newsDeliverer, NextClubMeeting $nextClubMeeting)
    {
        $this->newsDeliverer = $newsDeliverer;
        $this->entityManager = $entityManager;
        $this->nextClubMeeting = $nextClubMeeting;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Delivers next news.')
            ->setHelp('Delivers next news if not already send. Due date is next club meeting.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dueDate = $this->createDueDate($this->nextClubMeeting->calcNextMeetingDate());
        $sendDate = $this->createSendDate($dueDate);

        //send date is not yet
        if ($sendDate > new \DateTime()) {
            $io->caution('Delivery date is not yet.');

            return;
        }

        //news was already sent
        $newsletter = $this->entityManager->getRepository(NewsLetter::class)->findOneBy(['dueAt' => $dueDate]);
        if ($newsletter) {
            $io->comment('News is already delivered.');

            return;
        }

        $allReaders = $this->entityManager->getRepository(NewsReader::class)->findAll();
        $newsletter = new NewsLetter();
        $newsletter->setDueAt($dueDate)
                ->setNoRecipients(count($allReaders));

        $this->entityManager->persist($newsletter);
        $this->entityManager->flush();

        $this->newsDeliverer->deliver($dueDate, $allReaders);

        $io->success('News delivered.');
    }

    private function createDueDate(string $strDate): \DateTime
    {
        return \DateTime::createFromFormat(NextClubMeeting::DATE_FORMAT, $strDate);
    }

    private function createSendDate(\DateTime $dueDate): \DateTime
    {
        $sendDate = clone $dueDate;
        $sendDate->modify('-4 day');

        return $sendDate;
    }
}
