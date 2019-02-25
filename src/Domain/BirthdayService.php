<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Domain;

final class BirthdayService
{
    use PublishesDomainEvents;
    
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

    public function sendGreetings(XDate $date): void
    {
        $this->sendBirthdayGreetsTo(
            $this->employeeRepository->byBirthday($date)
        );
    }

    private function sendBirthdayGreetsTo(array $employees): void
    {
        foreach ($employees as $employee) {
            $this->birthdayGreetSender->send(
                new BirthdayGreet($employee)
            );

            $this->publishThat(BirthdayGreetWasSent::now($employee));
        }
    }
}
