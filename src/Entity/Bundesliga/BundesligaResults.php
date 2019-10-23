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
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaResultsRepository")
 */
class BundesligaResults
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $season;

    /**
     * @ORM\Column(type="smallint")
     */
    private $matchDay;

    /**
     * @ORM\Column(type="string", length=255)
     */

    private $homeTeam;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $awayTeam;

    /**
     * @ORM\Column(type="smallint")
     */
    private $pointsAwayTeam;

    /**
     * @ORM\Column(type="smallint")
     */
    private $pointsHomeTeam;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $boardPointsAwayTeam;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $boardPointsHomeTeam;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $playedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaDetails", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $details;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatchDay(): ?int
    {
        return $this->matchDay;
    }

    public function setMatchDay(int $matchDay): self
    {
        $this->matchDay = $matchDay;

        return $this;
    }

    public function getHomeTeam(): ?string
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(string $homeTeam): self
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?string
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(string $awayTeam): self
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getPointsAwayTeam(): ?int
    {
        return $this->pointsAwayTeam;
    }

    public function setPointsAwayTeam(int $pointsAwayTeam): self
    {
        $this->pointsAwayTeam = $pointsAwayTeam;

        return $this;
    }

    public function getPointsHomeTeam(): ?int
    {
        return $this->pointsHomeTeam;
    }

    public function setPointsHomeTeam(int $pointsHomeTeam): self
    {
        $this->pointsHomeTeam = $pointsHomeTeam;

        return $this;
    }

    public function getBoardPointsAwayTeam(): ?int
    {
        return $this->boardPointsAwayTeam;
    }

    public function setBoardPointsAwayTeam(int $boardPointsAwayTeam): self
    {
        $this->boardPointsAwayTeam = $boardPointsAwayTeam;

        return $this;
    }

    public function getBoardPointsHomeTeam(): ?int
    {
        return $this->boardPointsHomeTeam;
    }

    public function setBoardPointsHomeTeam(int $boardPointsHomeTeam): self
    {
        $this->boardPointsHomeTeam = $boardPointsHomeTeam;

        return $this;
    }

    public function getPlayedAt(): ?\DateTimeInterface
    {
        return $this->playedAt;
    }

    public function setPlayedAt(?\DateTimeInterface $playedAt): self
    {
        $this->playedAt = $playedAt;

        return $this;
    }

    public function getSeason(): ?BundesligaSeason
    {
        return $this->season;
    }

    public function setSeason(BundesligaSeason $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getDetails(): ?BundesligaDetails
    {
        return $this->details;
    }

    public function setDetails(?BundesligaDetails $details): self
    {
        $this->details = $details;

        return $this;
    }
}
