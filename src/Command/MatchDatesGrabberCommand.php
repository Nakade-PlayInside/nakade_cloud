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

use App\Repository\Bundesliga\BundesligaSeasonRepository;
use App\Tools\DGoB\Transfer\MatchDatesTransfer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MatchDatesGrabberCommand extends Command
{
    protected static $defaultName = 'app:matchDates:grabber';
    private $grabber;
    private $repository;

    public function __construct(BundesligaSeasonRepository $repository, MatchDatesTransfer $grabber)
    {
        parent::__construct();
        $this->grabber = $grabber;
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Grabs actual match dates from the DGoB Site.')
            ->setHelp('Prerequisite for this is an actual season. Stores new dates in a results table if found.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $actualSeason = $this->repository->findOneBy(['actualSeason' => true]);
        if (!$actualSeason) {
            $io->comment('No actual season found. Abort!');

            return 1;
        }

        // $transfer->transfer('2019_2020', '2');
        $table = $this->grabber->transfer($actualSeason->getDGoBIndex(), $actualSeason->getLeague());
        if (!$table) {
            $io->comment('New season still not set. Probably Next time!');

            return 1;
        }

        $io->success('New season found. Match dates added!');

        return 0;
    }
}
