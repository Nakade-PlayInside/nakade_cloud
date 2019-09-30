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

namespace App\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReCaptchaValidator!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ReCaptchaValidator extends ConstraintValidator
{
    const GOOGLE_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var string
     */
    private $secretKey;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ReCaptchaValidator constructor.
     *
     * @param string              $secretKey
     * @param RequestStack        $requestStack
     * @param TranslatorInterface $translator
     */
    public function __construct(string $secretKey, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->secretKey = $secretKey;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $response = $this->requestStack->getMasterRequest()->get('g-recaptcha-response');

        if (!$constraint instanceof ReCaptcha) {
            throw new UnexpectedTypeException($constraint, ReCaptcha::class);
        }

        if (null === $response || '' === $response) {
            $errorMessage = $this->translator->trans('notChecked', [], 'recaptcha');
            $this->context->buildViolation($errorMessage)->addViolation();

            return;
        }

        $apiRequest = curl_init();

        curl_setopt($apiRequest, CURLOPT_URL, self::GOOGLE_URL);
        curl_setopt($apiRequest, CURLOPT_HEADER, 0);
        curl_setopt($apiRequest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($apiRequest, CURLOPT_POST, true);
        curl_setopt($apiRequest, CURLOPT_POSTFIELDS, [
             'secret' => $this->secretKey,
             'response' => $response,
        ]);

        $apiResponse = curl_exec($apiRequest);
        curl_close($apiRequest);

        $data = json_decode($apiResponse, true);

        if (!$data['success']) {
            //if no error code is given
            if (0 === count($data['error-codes'])) {
                $errorMessage = $this->translator->trans('required', [], 'recaptcha');
                $this->context->buildViolation($errorMessage)->addViolation();
            }

            foreach ($data['error-codes'] as $errorCode) {
                $this->context->buildViolation($errorCode)->addViolation();
            }
        }
    }
}
