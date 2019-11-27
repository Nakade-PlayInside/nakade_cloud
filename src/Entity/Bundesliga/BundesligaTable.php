<?php

namespace App\Entity\Bundesliga;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaTableRepository")
 * @ORM\Table(
 *      name="bundesliga_table",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"season", "league", "games", "position", "match_day"}
 *      )}
 * )
 *
 * @UniqueEntity(
 *     fields={"season", "league", "games", "position", "matchDay"},
 *     message="table.unique"
 * )
 */
class BundesligaTable
{

    const TENDENCY_CHAMPION = 10;
    const TENDENCY_AUFSTEIGER = 20;
    const TENDENCY_RELEGATION = 30;
    const TENDENCY_ABSTEIGER = 40;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $season;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $league;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $matchDay;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $games;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $wins;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $draws;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $losses;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $boardPoints;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $points;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgSrc;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $tendency;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(string $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getLeague(): ?string
    {
        return $this->league;
    }

    public function setLeague(string $league): self
    {
        $this->league = $league;

        return $this;
    }

    public function getMatchDay(): ?string
    {
        return $this->matchDay;
    }

    public function setMatchDay(string $matchDay): self
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

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(string $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getGames(): ?string
    {
        return $this->games;
    }

    public function setGames(string $games): self
    {
        $this->games = $games;

        return $this;
    }

    public function getWins(): ?string
    {
        return $this->wins;
    }

    public function setWins(string $wins): self
    {
        $this->wins = $wins;

        return $this;
    }

    public function getDraws(): ?string
    {
        return $this->draws;
    }

    public function setDraws(string $draws): self
    {
        $this->draws = $draws;

        return $this;
    }

    public function getLosses(): ?string
    {
        return $this->losses;
    }

    public function setLosses(string $losses): self
    {
        $this->losses = $losses;

        return $this;
    }

    public function getBoardPoints(): ?string
    {
        return $this->boardPoints;
    }

    public function setBoardPoints(string $boardPoints): self
    {
        $this->boardPoints = $boardPoints;

        return $this;
    }

    public function getPoints(): ?string
    {
        return $this->points;
    }

    public function setPoints(string $points): self
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getTitle()
    {
        $season = $this->getSeason();
        $season = str_replace('_', '-', $season);

        return sprintf('%s. Bundesliga %s', $this->getLeague(), $season);
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

        if (false !== stripos($this->getTeam(), 'Nakade')) {
            $css .= ' nakade';
        }

        return $css;
    }
}
