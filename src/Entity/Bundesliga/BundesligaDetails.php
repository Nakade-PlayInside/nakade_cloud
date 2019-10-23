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
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaDetailsRepository")
 */
class BundesligaDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaMatch", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $firstBoard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaMatch", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $secondBoard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaMatch", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $thirdBoard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaMatch", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $fourthBoard;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $opponentTeam;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstBoard(): ?BundesligaMatch
    {
        return $this->firstBoard;
    }

    public function setFirstBoard(BundesligaMatch $firstBoard): self
    {
        $this->firstBoard = $firstBoard;

        return $this;
    }

    public function getSecondBoard(): ?BundesligaMatch
    {
        return $this->secondBoard;
    }

    public function setSecondBoard(BundesligaMatch $secondBoard): self
    {
        $this->secondBoard = $secondBoard;

        return $this;
    }

    public function getThirdBoard(): ?BundesligaMatch
    {
        return $this->thirdBoard;
    }

    public function setThirdBoard(BundesligaMatch $thirdBoard): self
    {
        $this->thirdBoard = $thirdBoard;

        return $this;
    }

    public function getFourthBoard(): ?BundesligaMatch
    {
        return $this->fourthBoard;
    }

    public function setFourthBoard(BundesligaMatch $fourthBoard): self
    {
        $this->fourthBoard = $fourthBoard;

        return $this;
    }

    public function getOpponentTeam(): ?string
    {
        return $this->opponentTeam;
    }

    public function setOpponentTeam(?string $opponentTeam): self
    {
        $this->opponentTeam = $opponentTeam;

        return $this;
    }
}
