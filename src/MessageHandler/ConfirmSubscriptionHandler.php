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
use App\Message\ConfirmSubscription;
use Swift_Mailer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

/**
 * Class ConfirmSubscriptionHandler!
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 * @copyright   Copyright (C) - 2019 Dr. Holger Maerz
 *
 * @author Dr. H.Maerz <holger@nakade.de>
 */
class ConfirmSubscriptionHandler implements MessageHandlerInterface
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

    public function __invoke(ConfirmSubscription $confirmSubscription)
    {
        /** @var ConfirmSubscription $confirmSubscription */
        $reader = $confirmSubscription->getNewsReader();
        $this->logger->notice(
            'Confirm subscription for reader {reader}, sentTo: <{sentTo}>',
            ['reader' => $reader, 'sentTo' => $reader->getEmail()]
        );

        $message = (new \Swift_Message('Bestätige deine email Adresse'))
                    ->setFrom($this->emailNoReply)
                    ->setTo($reader->getEmail())
                    ->setBody(
                        $this->twig->render('emails/confirmSubscription.html.twig', [
                            'email' => $reader->getEmail(),
                            'token' => $reader->getSubscribeToken(),
                        ]),
                        'text/html'
                    )

                    ->addPart($this->twig->render('emails/confirmSubscription.txt.twig', [
                            'email' => $reader->getEmail(),
                            'token' => $reader->getSubscribeToken(),
                    ]), 'text/plain');

        $sent = $this->mailer->send($message);

        if (0 === $sent) {
            if ($this->logger) {
                $this->logger->error(
                    'Could not sent confirm subscription to {reader} <{email}>',
                    ['reader' => $reader, 'email' => $reader->getEmail()]
                );
            }
        }
    }
}
