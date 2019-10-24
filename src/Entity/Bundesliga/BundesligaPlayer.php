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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaPlayerRepository")
 */
class BundesligaPlayer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDay;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bundesliga\BundesligaMatch", mappedBy="player")
     */
    private $matches;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Bundesliga\BundesligaSeason", mappedBy="players")
     */
    private $seasons;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->seasons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?\DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

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
            $match->setPlayer($this);
        }

        return $this;
    }

    public function removeMatch(BundesligaMatch $match): self
    {
        if ($this->matches->contains($match)) {
            $this->matches->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getPlayer() === $this) {
                $match->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BundesligaSeason[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(BundesligaSeason $bundesligaSeason): self
    {
        if (!$this->seasons->contains($bundesligaSeason)) {
            $this->seasons[] = $bundesligaSeason;
            $bundesligaSeason->addPlayer($this);
        }

        return $this;
    }

    public function removeSeason(BundesligaSeason $bundesligaSeason): self
    {
        if ($this->seasons->contains($bundesligaSeason)) {
            $this->seasons->removeElement($bundesligaSeason);
            $bundesligaSeason->removePlayer($this);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getAge(): ?string
    {
        if (!$this->birthDay) {
            return '';
        }

        return (string) (new \DateTime('now'))->diff($this->birthDay)->y;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
