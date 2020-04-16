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

use App\Entity\Bundesliga\BundesligaSgf;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

class SgfHashCommand extends Command
{
    protected static $defaultName = 'app:sgf:hash';
    private $entityManager;
    private $appKernel;

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->appKernel = $appKernel;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a hash of SGF files content.')
            ->setHelp('To prevent multiple SGF files, the content is hashed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $appDir = $this->appKernel->getProjectDir();
        $files = $this->entityManager->getRepository(BundesligaSgf::class)->findBy(['hash' => null]);
        foreach ($files as $sgf) {
            $path = $appDir.'/public/'.$sgf->getPath();
            if (!is_file($path)) {
                continue;
            }
            $hash = md5_file($path);
            $sgf->setHash($hash);
        }
        $this->entityManager->flush();

        $io->success('finished.');

        return 0;
    }
}
