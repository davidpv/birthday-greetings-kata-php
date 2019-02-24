<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Domain;

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
     * @var BirthdayGreetSender
     */
    private $birthdayGreetSender;

    public function __construct(EmployeeRepository $employeeRepository, BirthdayGreetSender $birthdayGreetSender)
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
