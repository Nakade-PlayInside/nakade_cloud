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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="smallint")
     */
    private $matchDay;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $pointsAway;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $pointsHome;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $boardPointsAway;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $boardPointsHome;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $playedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bundesliga\BundesligaMatch", mappedBy="results")
     */
    private $matches;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaTeam")
     * @ORM\JoinColumn(nullable=false)
     */
    private $home;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaTeam")
     * @ORM\JoinColumn(nullable=false)
     */
    private $away;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason")
     * @ORM\JoinColumn(nullable=false)
     */
    private $season;


    public function __construct()
    {
        $this->matches = new ArrayCollection();
    }

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

    public function getPointsAway(): ?int
    {
        return $this->pointsAway;
    }

    public function setPointsAway(int $points): self
    {
        $this->pointsAway = $points;

        return $this;
    }

    public function getPointsHome(): ?int
    {
        return $this->pointsHome;
    }

    public function setPointsHome(int $pointsHome): self
    {
        $this->pointsHome = $pointsHome;

        return $this;
    }

    public function getBoardPointsAway(): ?int
    {
        return $this->boardPointsAway;
    }

    public function setBoardPointsAway(int $boardPointsAway): self
    {
        $this->boardPointsAway = $boardPointsAway;

        return $this;
    }

    public function getBoardPointsHome(): ?int
    {
        return $this->boardPointsHome;
    }

    public function setBoardPointsHome(int $boardPointsHome): self
    {
        $this->boardPointsHome = $boardPointsHome;

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

    /**
     * @return Collection|BundesligaMatch[]
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    public function addMatch(BundesligaMatch $match): self
    {
        if (!$this->matches->contains($match)) {
            $this->matches[] = $match;
            $match->setResults($this);
        }

        return $this;
    }

    public function removeMatch(BundesligaMatch $match): self
    {
        if ($this->matches->contains($match)) {
            $this->matches->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getResults() === $this) {
                $match->setResults(null);
            }
        }

        return $this;
    }

    public function getHome(): ?BundesligaTeam
    {
        return $this->home;
    }

    public function setHome(?BundesligaTeam $home): self
    {
        $this->home = $home;

        return $this;
    }

    public function getAway(): ?BundesligaTeam
    {
        return $this->away;
    }

    public function setAway(?BundesligaTeam $away): self
    {
        $this->away = $away;

        return $this;
    }

    public function getSeason(): ?BundesligaSeason
    {
        return $this->season;
    }

    public function setSeason(?BundesligaSeason $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getPairing(): string
    {
        return $this->getHome()->getName().' - '.$this->getAway()->getName();
    }

    public function getResult(): string
    {
        return $this->getPointsHome().' : '.$this->getPointsAway();
    }
}
