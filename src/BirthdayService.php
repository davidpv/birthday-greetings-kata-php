<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

final class BirthdayService
{
    /**
     * @var EmployeeRepository
     */
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function sendGreetings(XDate $xDate, $smtpHost, $smtpPort): void
    {
        $employees = $this->employeeRepository->byBirthday($xDate);
        
        foreach ($employees as $employee) {
            $recipient = $employee->getEmail();
            $body = sprintf('Happy Birthday, dear %s!', $employee->getFirstName());
            $subject = 'Happy Birthday!';
            
            $birthdayGreet = new class($employee) {
                /**
                 * @var string
                 */
                private $from;
                
                /**
                 * @var string
                 */
                private $title;
                
                /**
                 * @var string
                 */
                private $message;
                
                /**
                 * @var string
                 */
                private $to;

                public function __construct(Employee $employee)
                {
                    $this->from = 'sender@here.com';
                    $this->title = 'Happy Birthday!';
                    $this->message = sprintf('Happy Birthday, dear %s!', $employee->getFirstName());
                    $this->to = $employee->getEmail();
                }

                public function from(): string
                {
                    return $this->from;
                }

                public function title(): string
                {
                    return $this->title;
                }

                public function message(): string
                {
                    return $this->message;
                }

                public function to(): string
                {
                    return $this->to;
                }
            };
            
            $this->sendMessage(
                $smtpHost,
                $smtpPort, 
                $birthdayGreet->from(),
                $birthdayGreet->title(),
                $birthdayGreet->message(),
                $birthdayGreet->to()
            );
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
