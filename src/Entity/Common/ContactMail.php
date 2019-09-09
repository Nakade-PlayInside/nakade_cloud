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

namespace App\Entity\Common;

use DateTime;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContactMail!
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 * @author Dr. H.Maerz <holger@nakade.de>
 *
 *
 * @ORM\Entity(repositoryClass="App\Repository\Common\ContactMailRepository")
 */
class ContactMail
{
    use TimestampableEntity;

    //todo: editor
    //todo: parentMailId wie forum oder eml abspeichern
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @var string|null
     */
    protected $city;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Email(
     *     message="Die Email {{ value }} ist ungültig.",
     *     checkMX=true
     * )
     *
     * @ORM\Column(type="string", length=160)
     *
     * @var string
     */
    protected $email;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    protected $firstName;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    protected $lastName;

    /**
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     *
     * @var string|null
     */
    protected $phone;

    /**
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    protected $address;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="text")
     *
     * @var string User message
     */
    protected $message;

    /**
     * @Assert\Type(
     *     type="string",
     *     message="Der Wert {{ value }} ist kein {{ type }}."
     * )
     *
     * @ORM\Column(type="string", length=12, nullable=true)
     *
     * @var string|null Postleitzahl
     */
    protected $zipCode;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return self
     */
    public function setAddress(string $address = ''): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return self
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return self
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return self
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return self
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     *
     * @return self
     */
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }
}
