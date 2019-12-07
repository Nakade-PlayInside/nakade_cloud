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
use App\Tools\DGoB\Transfer\ArchiveTransfer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArchivedDataGrabberCommand extends Command
{
    protected static $defaultName = 'app:archive:grabber';
    private $grabber;
    private $repository;

    public function __construct(BundesligaSeasonRepository $repository, ArchiveTransfer $grabber)
    {
        parent::__construct();
        $this->grabber = $grabber;
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->addArgument('DGoBIndex', InputArgument::REQUIRED, 'Year span of the season to grab eg. 2017_2018!')
            ->addArgument('league', InputArgument::REQUIRED, 'The league, you want to grab eg. 3b!')
        ;

        $this
            ->setDescription('Grabs archived matches of your team from the DGoB Site.')
            ->setHelp('Stores all matches found in match table if found. 
            Same is for team players and opponent players. WARNING! YOU PROBABLY HAVE TO EDIT A LOT SINCE 
             THE DGOB ARCHIVE IS FULL OF ERRORS!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dGobIndex = $input->getArgument('DGoBIndex');
        $league = $input->getArgument('league');

        // $transfer->transfer('2019_2020', '2');
        $table = $this->grabber->transfer($dGobIndex, $league);
        if (!$table) {
            $io->caution('No data found! Did you provide correct data?');

            return;
        }

        $io->success('Data found and stored!');
    }
}
