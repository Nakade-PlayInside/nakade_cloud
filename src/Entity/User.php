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

use App\Tools\TokenGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already registered!"
 * )
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
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

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
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min = 6)
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\Url(
     *     message="Die url {{ value }} ist ungültig."
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmToken;

    /**
     * Only a confirmed email address will receive mails.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $confirmed = false;

    /**
     * Has registered for newsletter.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $newsletter = false;

    /**
     * Account can be locked if compromised.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $locked = false;

    /**
     * Account can be disabled.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $disabled = false;

    /**
     * A removed user is inactive, not allowed to sign in and only visible to admins or superAdmins.
     *
     * @ORM\Column(type="boolean", options={"default": 0} )
     */
    private $removed = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $resetAt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->quotes = new ArrayCollection();
        $this->confirmToken = TokenGenerator::generateToken('confirm');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

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

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(?string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setAvatarUrl(string $avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getAvatarUrl(int $size = 32): string
    {
        if ($this->avatarUrl) {
            return $this->avatarUrl;
        }

        //show robos if no url is set
        $url = 'https://robohash.org/'.$this->getEmail();
        if ($size) {
            $url .= sprintf('?size=%dx%d', $size, $size);
        }

        return $url;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function setConfirmToken(string $token): self
    {
        $this->confirmToken = $token;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked)
    {
        $this->locked = $locked;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function hasNewsletter(): bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->getLastName();
    }

    public function __toString(): ?string
    {
        return $this->getFirstName();
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetAt(): ?\DateTimeInterface
    {
        return $this->resetAt;
    }

    public function setResetAt(?\DateTimeInterface $resetAt): self
    {
        $this->resetAt = $resetAt;

        return $this;
    }
}
