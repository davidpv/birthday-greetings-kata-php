<?php

declare(strict_types=1);

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;

require __DIR__ . '/../vendor/autoload.php';

$commandBus = new CommandBus([
    new CommandHandlerMiddleware(
        new ClassNameExtractor(),
        new League\Tactician\Handler\Locator\CallableLocator(function() {
            $birthdayService = new BirthdayGreetingsKata\Domain\BirthdayService(
                new BirthdayGreetingsKata\Infrastructure\Persistence\CsvEmployeeRepository(__DIR__ . '/../resources/employee_data.txt'),
                new BirthdayGreetingsKata\Infrastructure\Notification\SwiftmailerBirthdayGreetSender('127.0.0.1', 1025)
            );

            return new BirthdayGreetingsKata\Command\SendEmployeeBirthdayGreetsCommandHandler($birthdayService);
        }),
        new HandleInflector()
    )
]);

$commandBus->handle(
    new BirthdayGreetingsKata\Command\SendEmployeeBirthdayGreetsCommand('2008/10/08')
);
