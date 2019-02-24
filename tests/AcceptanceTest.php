<?php

declare(strict_types=1);

namespace Tests\BirthdayGreetingsKata;

use BirthdayGreetingsKata\Domain\BirthdayGreet;
use BirthdayGreetingsKata\Domain\BirthdayGreetSender;
use BirthdayGreetingsKata\Domain\BirthdayService;
use BirthdayGreetingsKata\Domain\Employee;
use BirthdayGreetingsKata\Domain\XDate;
use BirthdayGreetingsKata\Infrastructure\Notification\FakeBirthdayGreetSender;
use BirthdayGreetingsKata\Infrastructure\Persistence\InMemoryEmployeeRepository;
use PHPUnit\Framework\TestCase;

class AcceptanceTest extends TestCase
{
    /**
     * @var BirthdayService
     */
    private $service;

    /**
     * @var FakeBirthdayGreetSender
     */
    private $birthdayGreetSender;

    /** @before */
    protected function prepareBirthdayGreetService(): void
    {
        $employeeRepository = new InMemoryEmployeeRepository();
        $employeeRepository->add(new Employee('John', 'Doe', '1982/10/08', 'john.doe@foobar.com'));
        $employeeRepository->add(new Employee('Mary', 'Ann', '1975/03/11', 'mary.ann@foobar.com'));

        $this->birthdayGreetSender = new FakeBirthdayGreetSender();

        $this->service = new BirthdayService($employeeRepository, $this->birthdayGreetSender);
    }

    /**
     * @test
     */
    public function willSendGreetings_whenItsSomebodysBirthday(): void
    {
        $this->service->sendGreetings(
            new XDate('2008/10/08')
        );

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
        $this->service->sendGreetings(
            new XDate('2008/01/01')
        );

        $this->assertCount(0, $this->messagesSent(), 'what? messages?');
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
