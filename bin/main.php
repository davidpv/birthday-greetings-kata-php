<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$birthdayService = new BirthdayGreetingsKata\Domain\BirthdayService(
    new BirthdayGreetingsKata\Infrastructure\Persistence\CsvEmployeeRepository(__DIR__ . '/../resources/employee_data.txt'),
    new BirthdayGreetingsKata\Infrastructure\Notification\SwiftmailerBirthdayGreetSender('127.0.0.1', 1025)
);

$birthdayService->sendGreetings(
    new BirthdayGreetingsKata\Domain\XDate('2008/10/08')
);

