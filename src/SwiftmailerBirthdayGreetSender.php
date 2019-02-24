<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

final class SwiftmailerBirthdayGreetSender implements BirthdayGreetSender
{
    /**
     * @var string
     */
    private $smtpHost;

    /**
     * @var int
     */
    private $smtpPort;

    public function __construct(string $smtpHost, int $smtpPort)
    {
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
    }

    public function send(BirthdayGreet $birthdayGreet): void
    {
        // Create a mailer
        $mailer = new Swift_Mailer(
            new Swift_SmtpTransport($this->smtpHost, $this->smtpPort)
        );

        // Construct the message
        $msg = new Swift_Message($birthdayGreet->title());
        $msg
            ->setFrom($birthdayGreet->from())
            ->setTo([$birthdayGreet->to()])
            ->setBody($birthdayGreet->message());

        // Send the message
        $mailer->send($msg);
    }
}
