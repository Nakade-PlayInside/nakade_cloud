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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\BundesligaTeamRepository")
 *
 * @UniqueEntity(
 *     fields={"name"},
 *     message="team.unique"
 * )
 */
class BundesligaTeam
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Groups("main")
     */
    private $id;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Groups("main")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @Groups("main")
     */
    private $kgsId;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Bundesliga\BundesligaSeason", mappedBy="teams")
     */
    private $seasons;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $captain;

    /**
     * @Assert\Email(
     *     message="Die Email {{ value }} ist ungültig."
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->penalties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|BundesligaSeason[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(BundesligaSeason $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->addTeam($this);
        }

        return $this;
    }

    public function removeSeason(BundesligaSeason $season): self
    {
        if ($this->seasons->contains($season)) {
            $this->seasons->removeElement($season);
            $season->removeTeam($this);
        }

        return $this;
    }

    public function getCaptain(): ?string
    {
        return $this->captain;
    }

    public function setCaptain(?string $captain): self
    {
        $this->captain = $captain;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getKgsId(): ?string
    {
        return $this->kgsId;
    }

    public function setKgsId(string $kgsId): self
    {
        $this->kgsId = $kgsId;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}
