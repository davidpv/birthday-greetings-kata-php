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

    /**
     * @var SwiftmailerBirthdayGreetSender
     */
    private $birthdayGreetSender;

    public function __construct(EmployeeRepository $employeeRepository, SwiftmailerBirthdayGreetSender $birthdayGreetSender)
    {
        $this->employeeRepository = $employeeRepository;
        $this->birthdayGreetSender = $birthdayGreetSender;
    }

    public function sendGreetings(XDate $xDate): void
    {
        $employees = $this->employeeRepository->byBirthday($xDate);
        
        foreach ($employees as $employee) {
            $birthdayGreet = new BirthdayGreet($employee);
            $this->birthdayGreetSender->send($birthdayGreet);
        }
    }
}
