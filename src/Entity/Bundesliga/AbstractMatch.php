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
 * @ORM\MappedSuperclass()
 */
abstract class AbstractMatch implements MatchInterface
{
    const HOME_TEAM = 'Nakade';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaPlayer", inversedBy="matches")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $player;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason", inversedBy="matches")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $season;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $board;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $color = 'b';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $result = '0:0';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaOpponent", inversedBy="matches")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $opponent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $winByDefault = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?BundesligaPlayer
    {
        return $this->player;
    }

    public function setPlayer(?BundesligaPlayer $player): self
    {
        $this->player = $player;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getOpponent(): ?BundesligaOpponent
    {
        return $this->opponent;
    }

    public function setOpponent(?BundesligaOpponent $opponent): self
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function getBoard(): ?int
    {
        return $this->board;
    }

    public function setBoard(int $board): self
    {
        $this->board = $board;

        return $this;
    }

    public function setResult(?string $result): self
    {
        if ($result) {
            $this->result = $result;
        }

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function isWinByDefault(): ?bool
    {
        return $this->winByDefault;
    }

    public function setWinByDefault(bool $winByDefault): self
    {
        $this->winByDefault = $winByDefault;

        return $this;
    }

    public function getPairing()
    {
        return $this->getPlayer()->getName().' - '.$this->getOpponent()->getName();
    }

    public function getOpponentTeam(): ?BundesligaTeam
    {
        if (false !== stripos($this->getResults()->getHome()->getName(), self::HOME_TEAM) &&
            false !== stripos($this->getResults()->getAway()->getName(), self::HOME_TEAM)) {
            return null;
        } elseif (false !== stripos($this->getResults()->getHome()->getName(), self::HOME_TEAM)) {
            return $this->getResults()->getAway();
        }

        return $this->getResults()->getHome();
    }

    public function __toString()
    {
        return $this->getPlayer()->getName().' - '.$this->getOpponent()->getName();
    }
}
