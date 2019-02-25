<?php

declare(strict_types=1);

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;

require __DIR__ . '/../vendor/autoload.php';

$client = new Elastica\Client();

$logger = new Monolog\Logger(
    'birthday-greetings', 
    [new Monolog\Handler\ElasticSearchHandler($client, ['index' => 'logs', 'type' => 'log'])],
);

$domainEventLogger = new Monolog\Logger(
    'domain-events',
    [new \Monolog\Handler\ElasticSearchHandler($client, ['index' => 'events', 'type' => 'event'])],
);

$normalizer = new Symfony\Component\Serializer\Normalizer\PropertyNormalizer();
$serializer = new \Symfony\Component\Serializer\Serializer([$normalizer, new Symfony\Component\Serializer\Normalizer\DateTimeNormalizer()]);
$normalizer->setSerializer($serializer);

$domainEventsSubscriber = new BirthdayGreetingsKata\Infrastructure\Subscription\DomainEventsSubscriber();

$eventMiddleware = new League\Tactician\CommandEvents\EventMiddleware();

$eventMiddleware->addListener('command.received', function () use ($domainEventsSubscriber) {
    Ddd\Domain\DomainEventPublisher::instance()->subscribe($domainEventsSubscriber);
});

$eventMiddleware->addListener('command.handled', function () use ($domainEventLogger, $normalizer, $domainEventsSubscriber) {
    $domainEvents = $domainEventsSubscriber->domainEvents();
    if (0 === count($domainEvents)) {
        return;
    }
    $persister = new BirthdayGreetingsKata\Infrastructure\Subscription\DomainEventsPersister($domainEventLogger, $normalizer);
    $persister->persist($domainEvents);
});

$commandBus = new CommandBus([
    $eventMiddleware,
    new League\Tactician\Logger\LoggerMiddleware(new League\Tactician\Logger\Formatter\ClassPropertiesFormatter(), $logger),
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
