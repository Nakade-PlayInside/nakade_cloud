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

namespace App\Form\Model;

use App\Entity\Bundesliga\BundesligaExecutive;
use App\Entity\Bundesliga\BundesligaMatch;
use App\Entity\Bundesliga\BundesligaResults;
use App\Validator\OpponentMatch;
use App\Validator\PlayerMatch;
use App\Validator\ResultMatch;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 * @OpponentMatch()
 * @PlayerMatch()
 * @ResultMatch()
 */
class ResultModel
{
    private $results;
    private $firstBoardMatch;
    private $secondBoardMatch;
    private $thirdBoardMatch;
    private $fourthBoardMatch;

    /**
     * @Assert\NotNull()
     */
    private $executive;

    /**
     * @Assert\NotBlank()
     */
    private $nakadeCaptainName;

    /**
     * @Assert\Email()
     */
    private $nakadeCaptainEmail;

    /**
     * @Assert\NotBlank()
     */
    private $oppCaptainName;

    /**
     * @Assert\Email()
     */
    private $oppCaptainEmail;

    public function __construct(BundesligaResults $results)
    {
        $this->results = $results;
        $this->executive = $results->getSeason()->getExecutive();
        $this->nakadeCaptainName = $results->getTeamNakade()->getCaptain();
        $this->nakadeCaptainEmail = $results->getTeamNakade()->getEmail();
        $this->oppCaptainName = $results->getOpponentTeam()->getCaptain();
        $this->oppCaptainEmail = $results->getOpponentTeam()->getEmail();
        $this->initMatches($results);
    }

    public function getResults(): BundesligaResults
    {
        return $this->results;
    }

    public function getFirstBoardMatch(): ?BundesligaMatch
    {
        return $this->firstBoardMatch;
    }

    public function setFirstBoardMatch(BundesligaMatch $firstBoardMatch): self
    {
        $this->firstBoardMatch = $firstBoardMatch;

        return $this;
    }

    public function getSecondBoardMatch(): ?BundesligaMatch
    {
        return $this->secondBoardMatch;
    }

    public function setSecondBoardMatch(BundesligaMatch $secondBoardMatch): self
    {
        $this->secondBoardMatch = $secondBoardMatch;

        return $this;
    }

    public function getThirdBoardMatch(): ?BundesligaMatch
    {
        return $this->thirdBoardMatch;
    }

    public function setThirdBoardMatch(BundesligaMatch $thirdBoardMatch): self
    {
        $this->thirdBoardMatch = $thirdBoardMatch;

        return $this;
    }

    public function getFourthBoardMatch(): ?BundesligaMatch
    {
        return $this->fourthBoardMatch;
    }

    public function setFourthBoardMatch(BundesligaMatch $fourthBoardMatch): self
    {
        $this->fourthBoardMatch = $fourthBoardMatch;

        return $this;
    }

    public function getExecutive(): ?BundesligaExecutive
    {
        return $this->executive;
    }

    public function setExecutive(BundesligaExecutive $executive): self
    {
        $this->executive = $executive;
        $this->results->getSeason()->setExecutive($executive);

        return $this;
    }

    public function getNakadeCaptainName(): ?string
    {
        return $this->nakadeCaptainName;
    }

    public function setNakadeCaptainName(string $nakadeCaptainName): self
    {
        $this->nakadeCaptainName = $nakadeCaptainName;
        $this->getResults()->getTeamNakade()->setCaptain($nakadeCaptainName);

        return $this;
    }

    public function getNakadeCaptainEmail(): ?string
    {
        return $this->nakadeCaptainEmail;
    }

    public function setNakadeCaptainEmail(string $nakadeCaptainEmail): self
    {
        $this->nakadeCaptainEmail = $nakadeCaptainEmail;
        $this->getResults()->getTeamNakade()->setEmail($nakadeCaptainEmail);

        return $this;
    }

    public function getOppCaptainName(): ?string
    {
        return $this->oppCaptainName;
    }

    public function setOppCaptainName(string $oppCaptainName): self
    {
        $this->oppCaptainName = $oppCaptainName;
        $this->getResults()->getOpponentTeam()->setCaptain($oppCaptainName);

        return $this;
    }

    public function getOppCaptainEmail(): ?string
    {
        return $this->oppCaptainEmail;
    }

    public function setOppCaptainEmail(string $oppCaptainEmail): self
    {
        $this->oppCaptainEmail = $oppCaptainEmail;
        $this->getResults()->getOpponentTeam()->setEmail($oppCaptainEmail);

        return $this;
    }

    public function isNakadeHome(): bool
    {
        return false !== stripos($this->results->getHome()->getName(), 'Nakade');
    }

    /**
     * @return BundesligaMatch[]
     */
    public function getAllMatches(): array
    {
        $data = [];
        $data[] = $this->firstBoardMatch;
        $data[] = $this->secondBoardMatch;
        $data[] = $this->thirdBoardMatch;
        $data[] = $this->fourthBoardMatch;

        return $data;
    }

    public function getCurrentResult(): string
    {
        $homePoints = $awayPoints = 0;
        foreach ($this->getAllMatches() as $match) {
            if ('0:0' === $match->getResult()) {
                continue;
            }
            $points = explode(':', $match->getResult());
            $homePoints += (int) $points[0];
            $awayPoints += (int) $points[1];
        }

        return sprintf('%s:%s', $homePoints, $awayPoints);
    }

    public function isCompleted(): bool
    {
        foreach ($this->getAllMatches() as $match) {
            if (!$match->getResult() || '0:0' === $match->getResult()) {
                return false;
            }
        }

        return true;
    }

    public function hasLineup(): bool
    {
        foreach ($this->getAllMatches() as $match) {
            if (!$match->getPlayer()) {
                return false;
            }
        }

        return true;
    }

    public function isReadyForResultMail(): bool
    {
        return $this->executive && $this->nakadeCaptainName && $this->nakadeCaptainEmail && $this->isCompleted();
    }

    public function isReadyForLineupMail(): bool
    {
        return $this->hasLineup() && !$this->isCompleted() && $this->executive && $this->nakadeCaptainName && $this->nakadeCaptainEmail && $this->oppCaptainEmail;
    }

    private function initMatches(BundesligaResults $results)
    {
        foreach ($results->getMatches() as $match) {
            switch ($match->getBoard()) {
                case 1:
                    $this->firstBoardMatch = $match;
                    break;
                case 2:
                    $this->secondBoardMatch = $match;
                    break;
                case 3:
                    $this->thirdBoardMatch = $match;
                    break;
                case 4:
                    $this->fourthBoardMatch = $match;
                    break;
            }
        }

        if (!$this->firstBoardMatch) {
            $this->firstBoardMatch = $this->createMatch($results, 1);
        }
        if (!$this->secondBoardMatch) {
            $this->secondBoardMatch = $this->createMatch($results, 2);
        }
        if (!$this->thirdBoardMatch) {
            $this->thirdBoardMatch = $this->createMatch($results, 3);
        }
        if (!$this->fourthBoardMatch) {
            $this->fourthBoardMatch = $this->createMatch($results, 4);
        }
    }

    private function createMatch(BundesligaResults $results, int $board)
    {
        $match = new BundesligaMatch();
        $match->setSeason($results->getSeason())
                ->setBoard($board)
                ->setResults($results)
        ;

        //default is black
        if ($match->isHomeMatch() && 0 === $board % 2) {
            $match->setColor('w');
        }
        if (!$match->isHomeMatch() && 1 === $board % 2) {
            $match->setColor('w');
        }

        return $match;
    }
}
