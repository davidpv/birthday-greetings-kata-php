<?php

declare(strict_types=1);

namespace Tests\BirthdayGreetingsKata;

use BirthdayGreetingsKata\Command\SendEmployeeBirthdayGreetsCommand;
use BirthdayGreetingsKata\Command\SendEmployeeBirthdayGreetsCommandHandler;
use BirthdayGreetingsKata\Domain\BirthdayGreet;
use BirthdayGreetingsKata\Domain\BirthdayService;
use BirthdayGreetingsKata\Domain\Employee;
use BirthdayGreetingsKata\Infrastructure\Notification\FakeBirthdayGreetSender;
use BirthdayGreetingsKata\Infrastructure\Persistence\InMemoryEmployeeRepository;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Logger\Formatter\ClassPropertiesFormatter;
use League\Tactician\Logger\LoggerMiddleware;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class AcceptanceTest extends TestCase
{
    /**
     * @var FakeBirthdayGreetSender
     */
    private $birthdayGreetSender;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var TestHandler
     */
    private $handler;

    /** @before */
    protected function prepareBirthdayGreetService(): void
    {
        $employeeRepository = new InMemoryEmployeeRepository();
        $employeeRepository->add(new Employee('John', 'Doe', '1982/10/08', 'john.doe@foobar.com'));
        $employeeRepository->add(new Employee('Mary', 'Ann', '1975/03/11', 'mary.ann@foobar.com'));

        $this->birthdayGreetSender = new FakeBirthdayGreetSender();
        $commandHandler = new SendEmployeeBirthdayGreetsCommandHandler(
            new BirthdayService(
                $employeeRepository,
                $this->birthdayGreetSender
            )
        );

        $this->handler = new TestHandler();
        $monolog = new Logger('test', [$this->handler]);
        
        $this->commandBus = new CommandBus([
            new LoggerMiddleware(new ClassPropertiesFormatter(), $monolog),
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                new InMemoryLocator([
                    SendEmployeeBirthdayGreetsCommand::class => $commandHandler
                ]),
                new HandleInflector()
            )
        ]);
    }

    /**
     * @test
     */
    public function willSendGreetings_whenItsSomebodysBirthday(): void
    {
        $this->commandBus->handle(new SendEmployeeBirthdayGreetsCommand('2008/10/08'));

        $messages = $this->messagesSent();
        $this->assertCount(1, $messages, 'message not sent?');

        $message = $messages[0];
        $this->assertEquals('Happy Birthday, dear John!', $message['Content']['Body']);
        $this->assertEquals('Happy Birthday!', $message['Content']['Headers']['Subject'][0]);
        $this->assertCount(1, $message['Content']['Headers']['To']);
        $this->assertEquals('john.doe@foobar.com', $message['Content']['Headers']['To'][0]);
    }

    /**
     * @test
     */
    public function willNotSendEmailsWhenNobodysBirthday(): void
    {
        $this->commandBus->handle(new SendEmployeeBirthdayGreetsCommand('2008/01/01'));

        $this->assertCount(0, $this->messagesSent(), 'what? messages?');
    }

    /** @test */
    public function logsWhenCommandHandlerIsExecutedAndAMatchingBirthdayIsGiven(): void
    {
        $this->commandBus->handle(new SendEmployeeBirthdayGreetsCommand('2008/10/08'));

        $records = $this->handler->getRecords();
        $this->assertGreaterThan(0, count($records));
    }

    private function messagesSent(): array
    {
        $sentGreets = [];

        /** @var BirthdayGreet $sentGreet */
        foreach ($this->birthdayGreetSender->sentGreets() as $sentGreet) {
            $sentGreets[] = [
                'Content' => [
                    'Body' => $sentGreet->message(),
                    'Headers' => [
                        'Subject' => [$sentGreet->title()],
                        'To' => [$sentGreet->to()]
                    ]
                ]
            ];
        }

        return $sentGreets;
    }
}
