<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$birthdayService = new BirthdayGreetingsKata\BirthdayService();
$birthdayService->sendGreetings(
    __DIR__ . '/../resources/employee_data.txt',
    new BirthdayGreetingsKata\XDate('2008/10/08'),
    '127.0.0.1',
    1025
);
