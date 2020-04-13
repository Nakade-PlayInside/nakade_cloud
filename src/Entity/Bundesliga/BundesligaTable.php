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
namespace App\Entity\Bundesliga;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaTableRepository")
 * @ORM\Table(
 *      name="bundesliga_table",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"bundesliga_season_id", "bundesliga_team_id", "match_day"}
 *      )}
 * )
 *
 * @UniqueEntity(
 *     fields={"bundesligaSeason", "bundesligaTeam", "matchDay"},
 *     message="table.unique"
 * )
 */
class BundesligaTable
{
    use TimestampableEntity;

    const TENDENCY_CHAMPION   = 10;
    const TENDENCY_AUFSTEIGER = 20;
    const TENDENCY_RELEGATION = 30;
    const TENDENCY_ABSTEIGER  = 40;
    const IMG_POS_SAME = "lmo-tab0.gif";
    const IMG_POS_UP   = "lmo-tab1.gif";
    const IMG_POS_DOWN = "lmo-tab2.gif";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $matchDay = 1;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @ORM\Column(type="smallint")
     */
    private $games = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $wins = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $draws = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $losses = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $boardPoints = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $points = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgSrc = self::IMG_POS_SAME;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $tendency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaSeason")
     */
    private $bundesligaSeason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bundesliga\BundesligaTeam")
     */
    private $bundesligaTeam;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $penalty;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $firstBoardPoints = 0;

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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function addGame(): self
    {
        $this->games++;

        return $this;
    }

    public function getGames(): int
    {
        return $this->games;
    }

    public function setGames(int $games): self
    {
        $this->games = $games;

        return $this;
    }

    public function addWin(): self
    {
        $this->wins++;

        return $this;
    }

    public function getWins(): int
    {
        return $this->wins;
    }

    public function setWins(int $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function addDraw(): self
    {
        $this->draws++;

        return $this;
    }

    public function getDraws(): int
    {
        return $this->draws;
    }

    public function setDraws(int $draws): self
    {
        $this->draws = $draws;

        return $this;
    }

    public function addLoss(): self
    {
        $this->losses++;

        return $this;
    }

    public function getLosses(): int
    {
        return $this->losses;
    }

    public function setLosses(int $losses): self
    {
        $this->losses = $losses;

        return $this;
    }

    public function addBoardPoints(int $boardPoints): self
    {
        $this->boardPoints += $boardPoints;

        return $this;
    }

    public function getBoardPoints(): int
    {
        return $this->boardPoints;
    }

    public function setBoardPoints(int $boardPoints): self
    {
        $this->boardPoints = $boardPoints;

        return $this;
    }

    public function addPoints(int $points): self
    {
        $this->points += $points;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getImgSrc(): ?string
    {
        return $this->imgSrc;
    }

    public function setImgSrc(?string $imgSrc): self
    {
        $this->imgSrc = $imgSrc;

        return $this;
    }

    public function getTendency(): ?int
    {
        return $this->tendency;
    }

    public function setTendency(?int $tendency): self
    {
        $this->tendency = $tendency;

        return $this;
    }

    public function getLeague(): ?string
    {
        return $this->getBundesligaSeason()->getLeague();
    }

    public function getTitle()
    {
        $title = $this->getBundesligaSeason()->getDGoBIndex();
        $title = str_replace('_', '-', $title);

        return sprintf('%s. Bundesliga %s', $this->getBundesligaSeason()->getLeague(), $title);
    }

    public function getCSS()
    {
        $css = '';

        switch ($this->getTendency()) {
            case 10:
                $css = 'meister';
                break;
            case 20:
                $css = 'aufsteiger';
                break;
            case 30:
                $css = 'relegation';
                break;
            case 40:
                $css = 'absteiger';
                break;
        }

        if (false !== stripos($this->getBundesligaTeam()->getName(), 'Nakade')) {
            $css .= ' nakade';
        }

        return $css;
    }

    public function getBundesligaSeason(): ?BundesligaSeason
    {
        return $this->bundesligaSeason;
    }

    public function setBundesligaSeason(?BundesligaSeason $bundesligaSeason): self
    {
        $this->bundesligaSeason = $bundesligaSeason;

        return $this;
    }

    public function getBundesligaTeam(): ?BundesligaTeam
    {
        return $this->bundesligaTeam;
    }

    public function setBundesligaTeam(?BundesligaTeam $bundesligaTeam): self
    {
        $this->bundesligaTeam = $bundesligaTeam;

        return $this;
    }

    public function getPenalty(): ?int
    {
        return $this->penalty;
    }

    public function setPenalty(?int $penalty): self
    {
        $this->penalty = $penalty;

        return $this;
    }

    public function getFirstBoardPoints(): ?int
    {
        return $this->firstBoardPoints;
    }

    public function setFirstBoardPoints(?int $firstBoardPoints): self
    {
        $this->firstBoardPoints = $firstBoardPoints;

        return $this;
    }
}
