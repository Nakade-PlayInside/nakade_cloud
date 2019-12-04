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

namespace App\Entity\Bundesliga;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\LineupMailRepository")
 */
class LineupMail extends AbstractMail
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaResults", inversedBy="lineupMail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $results;

    public function __construct(BundesligaResults $results)
    {
        $this->results = $results;
    }

    public function getResults(): ?BundesligaResults
    {
        return $this->results;
    }

    public function setResults(BundesligaResults $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function getOpponentTeam(): BundesligaTeam
    {
        return $this->results->getOpponentTeam();
    }

    public function getFirstNameOppManager(): ?string
    {
        $nameOppCaptain = $this->results->getOpponentTeam()->getCaptain();
        if (!$nameOppCaptain) {
            return '';
        }
        $names = explode(' ', $nameOppCaptain);

        return array_shift($names);
    }

    public function getLeagueNumber(): int
    {
        return intval($this->results->getSeason()->getLeague());
    }

    public function getLeagueGroup(): string
    {
        return preg_replace('/[0-9]+/', '', $this->results->getSeason()->getLeague());
    }
}
