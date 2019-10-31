<?php

namespace App\Entity\Bundesliga;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaTeamLineupRepository")
 */
class BundesligaTeamLineup
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
     * @var ArrayCollection
     */
    private $players;

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

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

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
     * @return Collection|BundesligaPlayer[]
     */
    public function getPlayers(): Collection
    {
        $method = 'getPosition';
        $count = 1;

        while (method_exists($this, $method.$count)) {
            $myMethod = $method.$count;
            $player = $this->$myMethod();
            if ($player && !$this->players->contains($player)) {
                $this->players->add($player);
            }
            ++$count;
        }
    }

    public function getPosition1(): BundesligaPlayer
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

    public function setPosition5(BundesligaPlayer $position5): self
    {
        $this->position5 = $position5;

        return $this;
    }

    public function getPosition6(): ?BundesligaPlayer
    {
        return $this->position6;
    }

    public function setPosition6(BundesligaPlayer $position6): self
    {
        $this->position6 = $position6;

        return $this;
    }

    public function getPosition7(): ?BundesligaPlayer
    {
        return $this->position7;
    }

    public function setPosition7(BundesligaPlayer $position7): self
    {
        $this->position7 = $position7;

        return $this;
    }

    public function getPosition8(): ?BundesligaPlayer
    {
        return $this->position8;
    }

    public function setPosition8(BundesligaPlayer $position8): self
    {
        $this->position8 = $position8;

        return $this;
    }

    public function getPosition9(): ?BundesligaPlayer
    {
        return $this->position9;
    }

    public function setPosition9(BundesligaPlayer $position9): self
    {
        $this->position9 = $position9;

        return $this;
    }

    public function getPosition10(): ?BundesligaPlayer
    {
        return $this->position10;
    }

    public function setPosition10(BundesligaPlayer $position10): self
    {
        $this->position10 = $position10;

        return $this;
    }
}
