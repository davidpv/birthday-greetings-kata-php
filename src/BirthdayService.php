<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

final class BirthdayService
{
    public function sendGreetings($fileName, XDate $xDate, $smtpHost, $smtpPort): void
    {
        $employeeRepository = new CsvEmployeeRepository($fileName);
        $employees = $employeeRepository->byBirthday($xDate);
        
        foreach ($employees as $employee) {
            $recipient = $employee->getEmail();
            $body = sprintf('Happy Birthday, dear %s!', $employee->getFirstName());
            $subject = 'Happy Birthday!';
            $this->sendMessage($smtpHost, $smtpPort, 'sender@here.com', $subject, $body, $recipient);
        }
    }

    private function sendMessage($smtpHost, $smtpPort, $sender, $subject, $body, $recipient): void
    {
        // Create a mailer
        $mailer = new Swift_Mailer(
            new Swift_SmtpTransport($smtpHost, $smtpPort)
        );

        // Construct the message
        $msg = new Swift_Message($subject);
        $msg
            ->setFrom($sender)
            ->setTo([$recipient])
            ->setBody($body)
        ;

        // Send the message
        $mailer->send($msg);
    }
}
