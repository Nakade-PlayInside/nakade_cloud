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

use App\Entity\Common\Quotes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email(
     *     message="Die Email {{ value }} ist ungültig.",
     *     checkMX=true
     * )
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickName;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Dein Passwort muss mind. {{ limit }} Zeichen enthalten.",
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Common\Quotes", mappedBy="author")
     */
    private $quotes;

    /**
     * Only an active user is allowed to sign in.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $active = false;

    /**
     * Only a verified email address will receive mails.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $verified = false;

    /**
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verifyCode;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->quotes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    /**
     * @param string|null $nickName
     *
     * @return User
     */
    public function setNickName(?string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Quotes[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    /**
     * @param Quotes $quote
     *
     * @return User
     */
    public function addQuote(Quotes $quote): self
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes[] = $quote;
            $quote->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Quotes $quote
     *
     * @return User
     */
    public function removeQuote(Quotes $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
            // set the owning side to null (unless already changed)
            if ($quote->getAuthor() === $this) {
                $quote->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return User
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     *
     * @return User
     */
    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVerifyCode(): ?string
    {
        return $this->verifyCode;
    }

    /**
     * @param string $verifyCode
     *
     * @return User
     */
    public function setVerifyCode(string $verifyCode): self
    {
        $this->verifyCode = $verifyCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName.' '.$this->getLastName();
    }

    /**
     * @param int $size
     *
     * @return string
     */
    public function getAvatarUrl(int $size = 32): string
    {
        $url = 'https://robohash.org/'.$this->getEmail();

        if ($size) {
            $url .= sprintf('?size=%dx%d', $size, $size);
        }

        return $url;
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->getFirstName();
    }
}
