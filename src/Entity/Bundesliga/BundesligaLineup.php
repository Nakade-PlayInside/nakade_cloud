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

use App\Validator\UniquePosition;
use App\Validator\UniqueSeasonLineup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaLineupRepository")
 *
 * @UniquePosition()
 */
class BundesligaLineup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason", inversedBy="lineup")
     * @ORM\JoinColumn(nullable=false)
     *
     * @UniqueSeasonLineup()
     */
    private $season;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $position1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $position2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $position3;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $position4;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     */
    private $position5;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     */
    private $position6;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     */
    private $position7;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     */
    private $position8;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     */
    private $position9;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer")
     */
    private $position10;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return BundesligaPlayer[]
     */
    public function getPlayers(): array
    {
        $method = 'getPosition';
        $count = 1;

        while (method_exists($this, $method.$count)) {
            $myMethod = $method.$count;
            $player = $this->$myMethod();

            if ($player) {
                $allPlayers[$count] = $player;
            }
            ++$count;
        }

        return array_unique($allPlayers);
    }

    public function getNumberOfPlayers(): int
    {
        return count($this->getPlayers());
    }

    public function getPosition1(): ?BundesligaPlayer
    {
        return $this->position1;
    }

    public function setPosition1(BundesligaPlayer $position1): self
    {
        $this->position1 = $position1;

        return $this;
    }

    public function getPosition2(): ?BundesligaPlayer
    {
        return $this->position2;
    }

    public function setPosition2(BundesligaPlayer $position2): self
    {
        $this->position2 = $position2;

        return $this;
    }

    public function getPosition3(): ?BundesligaPlayer
    {
        return $this->position3;
    }

    public function setPosition3(BundesligaPlayer $position3): self
    {
        $this->position3 = $position3;

        return $this;
    }

    public function getPosition4(): ?BundesligaPlayer
    {
        return $this->position4;
    }

    public function setPosition4(BundesligaPlayer $position4): self
    {
        $this->position4 = $position4;

        return $this;
    }

    public function getPosition5(): ?BundesligaPlayer
    {
        return $this->position5;
    }

    public function setPosition5(?BundesligaPlayer $position5): self
    {
        $this->position5 = $position5;

        return $this;
    }

    public function getPosition6(): ?BundesligaPlayer
    {
        return $this->position6;
    }

    public function setPosition6(?BundesligaPlayer $position6): self
    {
        $this->position6 = $position6;

        return $this;
    }

    public function getPosition7(): ?BundesligaPlayer
    {
        return $this->position7;
    }

    public function setPosition7(?BundesligaPlayer $position7): self
    {
        $this->position7 = $position7;

        return $this;
    }

    public function getPosition8(): ?BundesligaPlayer
    {
        return $this->position8;
    }

    public function setPosition8(?BundesligaPlayer $position8): self
    {
        $this->position8 = $position8;

        return $this;
    }

    public function getPosition9(): ?BundesligaPlayer
    {
        return $this->position9;
    }

    public function setPosition9(?BundesligaPlayer $position9): self
    {
        $this->position9 = $position9;

        return $this;
    }

    public function getPosition10(): ?BundesligaPlayer
    {
        return $this->position10;
    }

    public function setPosition10(?BundesligaPlayer $position10): self
    {
        $this->position10 = $position10;

        return $this;
    }

    public function __toString()
    {
        return 'Lineup';
    }
}
