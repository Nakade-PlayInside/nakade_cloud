<?php

namespace App\Entity\Bundesliga;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\LineupMailRepository")
 */
class LineupMail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bundesliga\BundesligaResults", inversedBy="lineupMail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $results;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sendAt;

    public function __construct(BundesligaResults $results)
    {
        $this->results = $results;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResults(): ?BundesligaResults
    {
        return $this->results;
    }

    public function setResults(?BundesligaResults $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(?\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getOpponentTeam(): BundesligaTeam
    {
        return $this->results->getOpponentTeam();
    }

    public function getFirstNameOppManager(): ?string
    {
        $nameOppCaptain = $this->results->getOpponentTeam()->getCaptain();
        if (!$nameOppCaptain) {
            return '';
        }
        $names = explode(' ', $nameOppCaptain);

        return array_shift($names);
    }

    public function getLeagueNumber(): int
    {
        return intval($this->results->getSeason()->getLeague());
    }

    public function getLeagueGroup(): string
    {
        return preg_replace('/[0-9]+/', '', $this->results->getSeason()->getLeague());
    }
}
