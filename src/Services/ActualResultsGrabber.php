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

use App\Entity\Bundesliga\BundesligaResults;
use App\Entity\Bundesliga\BundesligaTable;
use App\Tools\Bundesliga\ResultsCatcher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Retrieve the actual result table from the DGoB site. Return an empty array if no new table is found.
 * If a table is found it is persisted in the database.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ActualResultsGrabber extends AbstractTableService
{
    private $resultsCatcher;
    private $logger;

    public function __construct(EntityManagerInterface $manager, ResultsCatcher $resultsCatcher, LoggerInterface $logger)
    {
        parent::__construct($manager);
        $this->resultsCatcher = $resultsCatcher;
        $this->logger = $logger;
    }

    public function retrieveTable(): ?array
    {
        $actualSeason = $this->findActualSeason();
        if (!$actualSeason) {
            return null;
        }

        $matchDay = $this->manager->getRepository(BundesligaResults::class)->findMatchDayUnplayed($actualSeason);
        if (!$matchDay) {
            $matchDay = '1';
        }
        /** @var BundesligaTable[] $table */
        $table = $this->resultsCatcher->extract($actualSeason, $matchDay);

        return $table;
    }
}
