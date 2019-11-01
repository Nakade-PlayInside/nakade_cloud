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
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Pairing;
use App\Validator\SeasonDate;

/**
 * @ORM\MappedSuperclass()
 *
 * @Pairing()
 *
 * @SeasonDate
 */
abstract class AbstractResults implements ResultsInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Assert\PositiveOrZero()
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $boardPointsAway = 0;

    /**
     * @Assert\PositiveOrZero()
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $boardPointsHome = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $playedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaTeam")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $home;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaTeam")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $away;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $season;

    public function getId(): ?int
    {
        return $this->id;
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
        return $this->getBoardPointsHome().' : '.$this->getBoardPointsAway();
    }
}
