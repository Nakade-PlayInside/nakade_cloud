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

namespace App\Validator\ReCaptcha;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ReCaptchaVerifier!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ReCaptchaVerifier
{
    private const GOOGLE_URL = 'https://www.google.com/recaptcha/api/siteverify';

    const NOT_CHECKED = 'notChecked';
    const SUCCESS = 'success';
    const ERROR_CODES = 'error-codes';

    private $secretKey;
    private $requestStack;

    public function __construct(string $secretKey, RequestStack $requestStack)
    {
        $this->secretKey = $secretKey;
        $this->requestStack = $requestStack;
    }

    /**
     * @return mixed Associated array
     */
    public function verify()
    {
        $response = $this->requestStack->getMasterRequest()->get('g-recaptcha-response');

        if (null === $response || '' === $response) {
            return [self::NOT_CHECKED => true];
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

        return json_decode($apiResponse, true);
    }
}
