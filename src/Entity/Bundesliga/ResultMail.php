<?php

namespace App\Entity\Bundesliga;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Bundesliga\ResultMailRepository")
 */
class ResultMail extends AbstractMail
{
    public function getSendTo(): ?BundesligaExecutive
    {
        return $this->results->getSeason()->getExecutive();
    }
}
