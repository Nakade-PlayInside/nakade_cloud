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

namespace App\Form\Model;

use App\Validator\Password;
use App\Validator\ReCaptcha;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserRegistrationFormModel!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class UserRegistrationFormModel
{
    /**
     * @Assert\NotBlank
     * @Assert\Email(
     *     message="Die Email {{ value }} ist ungültig.",
     *     checkMX=true
     * )
     * @UniqueUser()
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="6")
     * @Password()
     */
    public $plainPassword;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $firstName;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $lastName;

    public $newsletter;

    /**
     * @ReCaptcha()
     */
    public $captcha;
}
