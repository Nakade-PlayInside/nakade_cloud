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
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoronaNewsRepository")
 */
class CoronaNews
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
    private $dueAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sendAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $noRecipients;

    public function __construct()
    {
        $this->sendAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDueAt(): ?\DateTimeInterface
    {
        return $this->dueAt;
    }

    public function setDueAt(\DateTimeInterface $dueAt): self
    {
        $this->dueAt = $dueAt;

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
