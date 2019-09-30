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

namespace App\MessageHandler;

use App\Message\ConfirmContact;
use Swift_Mailer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

/**
 * Class ConfirmContactHandler!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ConfirmContactHandler implements MessageHandlerInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * ConfirmContactHandler constructor.
     *
     * @param Swift_Mailer $mailer
     * @param Environment  $twig
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param ConfirmContact $confirmContact
     */
    public function __invoke(ConfirmContact $confirmContact)
    {
        $contactMail = $confirmContact->getContactMail();

        $message = (new \Swift_Message('Ihre Kontaktanfrage'))
                    ->setFrom('noreply@nakade.de')
                    ->setTo($contactMail->getEmail())
                    ->setBody(
                        $this->twig->render(
                            // templates/emails/registration.html.twig
                                    'emails/contact.html.twig',
                            ['name' => $contactMail->getName()]
                        ),
                        'text/html'
                    )

                    // you can remove the following code if you don't define a text version for your emails
                    ->addPart(
                        $this->twig->render(
                            // templates/emails/registration.txt.twig
                                    'emails/contact.txt.twig',
                            ['name' => $contactMail->getName()]
                        ),
                        'text/plain'
                    );
        $this->mailer->send($message);
    }
}
