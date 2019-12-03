<?php

namespace App\Entity\Bundesliga;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\LineupMailRepository")
 */
class LineupMail extends AbstractMail
{
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
}
