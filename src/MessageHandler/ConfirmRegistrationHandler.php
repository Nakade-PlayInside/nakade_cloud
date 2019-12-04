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

use App\Logger\MailLoggerTrait;
use App\Message\ConfirmRegistration;
use Swift_Mailer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

/**
 * Class ConfirmRegistrationHandler!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ConfirmRegistrationHandler implements MessageHandlerInterface
{
    use MailLoggerTrait;

    private $mailer;
    private $twig;
    private $emailNoReply;

    public function __construct(Swift_Mailer $mailer, Environment $twig, string $emailNoReply)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailNoReply = $emailNoReply;
    }

    public function __invoke(ConfirmRegistration $confirmRegistration)
    {
        $user = $confirmRegistration->getUser();
        $this->logger->notice(
            'confirmation mail to user {user}, sentTo <{email}>',
            ['user' => $user, 'email' => $user->getEmail()]
        );

        $message = (new \Swift_Message('Bestätige deine email Adresse'))
                    ->setFrom($this->emailNoReply)
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->twig->render('emails/confirmRegistration.html.twig', [
                            'email' => $user->getEmail(),
                            'name' => $user->getFullName(),
                            'token' => $user->getConfirmToken(),
                        ]),
                        'text/html'
                    )

                    ->addPart($this->twig->render('emails/confirmRegistration.txt.twig', [
                            'email' => $user->getEmail(),
                            'name' => $user->getFullName(),
                            'token' => $user->getConfirmToken(),
                    ]), 'text/plain');

        $sent = $this->mailer->send($message);

        if (0 === $sent) {
            if ($this->logger) {
                $this->logger->error(
                    'Could not sent confirmation mail to user {user} <{email}>',
                    ['user' => $user, 'email' => $user->getEmail()]
                );
            }
        }
    }
}
