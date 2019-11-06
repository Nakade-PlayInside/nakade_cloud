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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaPenaltyRepository")
 * @UniqueEntity(
 *    fields={"season", "team"},
 *    message="penalty.unique"
 * )
 */
class BundesligaPenalty
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaTeam", inversedBy="penalties")
     */
    private $team;

    /**
     * @Assert\NegativeOrZero()
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $boardPointPenalty;

    /**
     * @Assert\NegativeOrZero()
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $scorePointPenalty;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason")
     * @ORM\JoinColumn(nullable=false)
     */
    private $season;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoardPointPenalty(): ?int
    {
        return $this->boardPointPenalty;
    }

    public function setBoardPointPenalty(?int $boardPointPenalty): self
    {
        $this->boardPointPenalty = $boardPointPenalty;

        return $this;
    }

    public function getScorePointPenalty(): ?int
    {
        return $this->scorePointPenalty;
    }

    public function setScorePointPenalty(?int $scorePointPenalty): self
    {
        $this->scorePointPenalty = $scorePointPenalty;

        return $this;
    }

    public function getTeam(): ?BundesligaTeam
    {
        return $this->team;
    }

    public function setTeam(?BundesligaTeam $team): self
    {
        $this->team = $team;

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
}
