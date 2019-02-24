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
        $fileHandler = fopen($fileName, 'rb');
        fgetcsv($fileHandler);
        $employees = [];

        while ($employeeData = fgetcsv($fileHandler, null)) {
            $employeeData = array_map('trim', $employeeData);
            $employee = new Employee($employeeData[1], $employeeData[0], $employeeData[2], $employeeData[3]);
            if ($employee->isBirthday($xDate)) {
                $employees[] = $employee;
            }
        }

        $employeeRepository = new class($fileName) {
            /**
             * @var string
             */
            private $fileName;
            
            public function __construct(string $fileName)
            {
                $this->fileName = $fileName;
            }

            public function byBirthday(XDate $xDate): array
            {
                $fileHandler = fopen($this->fileName, 'rb');
                fgetcsv($fileHandler);
                $employees = [];

                while ($employeeData = fgetcsv($fileHandler, null)) {
                    $employeeData = array_map('trim', $employeeData);
                    $employee = new Employee($employeeData[1], $employeeData[0], $employeeData[2], $employeeData[3]);
                    if ($employee->isBirthday($xDate)) {
                        $employees[] = $employee;
                    }
                }
                
                return $employees;
            }
        };
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
