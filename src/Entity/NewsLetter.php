<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsLetterRepository")
 */
class NewsLetter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $meetingAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sendAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $noRecipients;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeetingAt(): ?\DateTimeInterface
    {
        return $this->meetingAt;
    }

    public function setMeetingAt(\DateTimeInterface $meetingAt): self
    {
        $this->meetingAt = $meetingAt;

        return $this;
    }

    public function getSendAt(): ?\DateTimeInterface
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getNoRecipients(): ?int
    {
        return $this->noRecipients;
    }

    public function setNoRecipients(int $noRecipients): self
    {
        $this->noRecipients = $noRecipients;

        return $this;
    }
}
