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
    private $birthdayGreetSender;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function sendGreetings(XDate $xDate, $smtpHost, $smtpPort): void
    {
        $employees = $this->employeeRepository->byBirthday($xDate);

        $this->birthdayGreetSender = new SwiftmailerBirthdayGreetSender($smtpHost, $smtpPort);
        
        foreach ($employees as $employee) {
            $birthdayGreet = new BirthdayGreet($employee);
            $this->birthdayGreetSender->send($birthdayGreet);
        }
    }
}
